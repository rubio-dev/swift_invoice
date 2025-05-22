document.addEventListener('DOMContentLoaded', function() {
    const typeSelect     = document.getElementById('type_select');
    const productSelect  = document.getElementById('product_id');
    const priceInput     = document.getElementById('price');
    const taxInput       = document.getElementById('tax_rate'); // Ahora es input
    const quantityInput  = document.getElementById('quantity');
    const addProductBtn  = document.getElementById('add-product');
    const productTable   = document.getElementById('product-table').getElementsByTagName('tbody')[0];
    const subtotalSpan   = document.getElementById('subtotal');
    const taxAmountSpan  = document.getElementById('tax-amount');
    const totalSpan      = document.getElementById('total');
    const submitBtn      = document.querySelector('#sale-form button[type="submit"]');

    // Llenar catálogo filtrado por tipo
function fillProductCatalog() {
    productSelect.innerHTML = '<option value="">Seleccionar...</option>';

    const selectedType = typeSelect.value;

    allProducts.forEach(prod => {
        // Bypass del filtro: mientras todos tengan type="Producto", mostrar todo
        const opt = document.createElement('option');
        opt.value = prod.id;
        opt.text  = prod.name + ' ($' + Number(prod.price).toFixed(2) + ')';
        opt.setAttribute('data-price', prod.price);
        productSelect.appendChild(opt);
    });

    priceInput.value = '';
}

    typeSelect.addEventListener('change', fillProductCatalog);

    // Sugerir precio cuando se selecciona catálogo (editable)
    productSelect.addEventListener('change', function() {
        const selected = productSelect.selectedOptions[0];
        priceInput.value = selected && selected.getAttribute('data-price') ? selected.getAttribute('data-price') : '';
    });

    // Agregar producto/servicio a la venta
    addProductBtn.addEventListener('click', function() {
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex]
            ? productSelect.options[productSelect.selectedIndex].text.split('($')[0].trim()
            : '';
        const productPrice = parseFloat(priceInput.value);
        const quantity = parseInt(quantityInput.value);
        let taxRate = parseFloat(taxInput.value);
        if (isNaN(taxRate) || taxRate < 0) taxRate = 0.00;

        if (!productId || isNaN(quantity) || quantity < 1 || isNaN(productPrice) || productPrice < 0) {
            alert('Completa todos los campos correctamente.');
            return;
        }

        // Calculo subtotal y impuesto por línea
        const subtotal = productPrice * quantity;
        const taxAmt = subtotal * (taxRate / 100);

        // Agregar a la tabla
        const rowIndex = productTable.rows.length;
        const row = productTable.insertRow();
        row.setAttribute('data-index', rowIndex);
        row.innerHTML = `
            <td>${productName}</td>
            <td>$${productPrice.toFixed(2)}</td>
            <td>${quantity}</td>
            <td>${taxRate.toFixed(2)}%</td>
            <td>$${(subtotal + taxAmt).toFixed(2)}</td>
            <td>
                <button type="button" class="DeleteBtn remove-product">Eliminar</button>
                <input type="hidden" name="products[${rowIndex}][id]" value="${productId}">
                <input type="hidden" name="products[${rowIndex}][name]" value="${productName}">
                <input type="hidden" name="products[${rowIndex}][price]" value="${productPrice}">
                <input type="hidden" name="products[${rowIndex}][quantity]" value="${quantity}">
                <input type="hidden" name="products[${rowIndex}][tax_rate]" value="${taxRate}">
            </td>
        `;

        // Guardar producto en sesión (AJAX)
        saveProductToSession({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: quantity,
            tax_rate: taxRate
        });

        updateTotals();
        productSelect.value = '';
        priceInput.value = '';
        taxInput.value = '0.00';
        quantityInput.value = 1;
        submitBtn.disabled = false;
    });

    // Eliminar producto de la venta
    productTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            const row = e.target.closest('tr');
            const rowIndex = row.getAttribute('data-index');
            removeProductFromSession(rowIndex);
            row.remove();
            reindexTableRows();
            updateTotals();
            if (productTable.rows.length === 0) submitBtn.disabled = true;
        }
    });

    // Guardar producto en sesión (via AJAX)
    function saveProductToSession(product) {
        fetch('save_product_session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(product)
        });
    }

    // Eliminar producto de la sesión (via AJAX)
    function removeProductFromSession(index) {
        fetch('remove_product_session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ index: index })
        });
    }

    // Reindexar filas de la tabla después de eliminar
    function reindexTableRows() {
        const rows = productTable.rows;
        for (let i = 0; i < rows.length; i++) {
            rows[i].setAttribute('data-index', i);
            const inputs = rows[i].querySelectorAll('input[type="hidden"]');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${i}]`));
            });
        }
    }

    // Actualizar totales (considerando impuesto por línea)
    function updateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let total = 0;

        const rows = productTable.rows;
        for (let i = 0; i < rows.length; i++) {
            const price = parseFloat(rows[i].querySelector('input[name*="[price]"]').value);
            const quantity = parseInt(rows[i].querySelector('input[name*="[quantity]"]').value);
            const taxRate = parseFloat(rows[i].querySelector('input[name*="[tax_rate]"]').value) || 0;

            const lineSubtotal = price * quantity;
            const lineTax = lineSubtotal * (taxRate / 100);

            subtotal += lineSubtotal;
            totalTax += lineTax;
        }

        total = subtotal + totalTax;

        subtotalSpan.textContent = `$${subtotal.toFixed(2)}`;
        taxAmountSpan.textContent = `$${totalTax.toFixed(2)}`;
        totalSpan.textContent = `$${total.toFixed(2)}`;

        // Actualizar inputs hidden
        document.querySelector('input[name="subtotal"]').value = subtotal;
        document.querySelector('input[name="tax_amount"]').value = totalTax;
        document.querySelector('input[name="total"]').value = total;
        // (tax_percentage general si quieres, por ahora solo suma)
    }

    // Inicialización
    fillProductCatalog();
});
