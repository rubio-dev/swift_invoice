document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const addProductBtn = document.getElementById('add-product');
    const productTable = document.getElementById('product-table').getElementsByTagName('tbody')[0];
    const saleForm = document.getElementById('sale-form');
    const subtotalSpan = document.getElementById('subtotal');
    const taxAmountSpan = document.getElementById('tax-amount');
    const totalSpan = document.getElementById('total');
    
    // Agregar producto a la venta
    addProductBtn.addEventListener('click', function() {
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex].text.split('($')[0].trim();
        const productPrice = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);
        const quantity = parseInt(quantityInput.value);
        
        if (!productId || isNaN(quantity) || quantity < 1) {
            alert('Por favor seleccione un producto y una cantidad válida');
            return;
        }
        
        // Agregar producto a la tabla
        const row = productTable.insertRow();
        const rowIndex = productTable.rows.length - 1;
        
        row.setAttribute('data-index', rowIndex);
        row.innerHTML = `
            <td>${productName}</td>
            <td>$${productPrice.toFixed(2)}</td>
            <td>${quantity}</td>
            <td>$${(productPrice * quantity).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-product">Eliminar</button>
                <input type="hidden" name="products[${rowIndex}][id]" value="${productId}">
                <input type="hidden" name="products[${rowIndex}][name]" value="${productName}">
                <input type="hidden" name="products[${rowIndex}][price]" value="${productPrice}">
                <input type="hidden" name="products[${rowIndex}][quantity]" value="${quantity}">
            </td>
        `;
        
        // Guardar producto en sesión (para persistencia al recargar)
        saveProductToSession({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: quantity
        });
        
        // Actualizar totales
        updateTotals();
        
        // Resetear selección
        productSelect.value = '';
        quantityInput.value = 1;
        
        // Habilitar botón de guardar
        document.querySelector('#sale-form button[type="submit"]').disabled = false;
    });
    
    // Eliminar producto de la venta
    productTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            const row = e.target.closest('tr');
            const rowIndex = row.getAttribute('data-index');
            
            // Eliminar producto de la sesión
            removeProductFromSession(rowIndex);
            
            // Eliminar fila
            row.remove();
            
            // Reindexar filas restantes
            reindexTableRows();
            
            // Actualizar totales
            updateTotals();
            
            // Deshabilitar botón de guardar si no hay productos
            if (productTable.rows.length === 0) {
                document.querySelector('#sale-form button[type="submit"]').disabled = true;
            }
        }
    });
    
    // Guardar producto en sesión (via AJAX)
    function saveProductToSession(product) {
        fetch('save_product_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(product)
        });
    }
    
    // Eliminar producto de la sesión (via AJAX)
    function removeProductFromSession(index) {
        fetch('remove_product_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ index: index })
        });
    }
    
    // Reindexar filas de la tabla después de eliminar
    function reindexTableRows() {
        const rows = productTable.rows;
        for (let i = 0; i < rows.length; i++) {
            rows[i].setAttribute('data-index', i);
            
            // Actualizar los índices en los inputs hidden
            const inputs = rows[i].querySelectorAll('input[type="hidden"]');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${i}]`));
            });
        }
    }
    
    // Actualizar totales
    function updateTotals() {
        const products = [];
        const rows = productTable.rows;
        
        for (let i = 0; i < rows.length; i++) {
            const price = parseFloat(rows[i].querySelector('input[name*="[price]"]').value);
            const quantity = parseInt(rows[i].querySelector('input[name*="[quantity]"]').value);
            
            products.push({
                price: price,
                quantity: quantity
            });
        }
        
        // Calcular totales
        const subtotal = products.reduce((sum, product) => sum + (product.price * product.quantity), 0);
        const taxPercentage = 16; // IVA 16%
        const taxAmount = subtotal * (taxPercentage / 100);
        const total = subtotal + taxAmount;
        
        // Actualizar UI
        subtotalSpan.textContent = `$${subtotal.toFixed(2)}`;
        taxAmountSpan.textContent = `$${taxAmount.toFixed(2)}`;
        totalSpan.textContent = `$${total.toFixed(2)}`;
        
        // Actualizar inputs hidden
        document.querySelector('input[name="subtotal"]').value = subtotal;
        document.querySelector('input[name="tax_amount"]').value = taxAmount;
        document.querySelector('input[name="total"]').value = total;
    }
});