document.addEventListener('DOMContentLoaded', () => {
  // Elementos del DOM
  const typeSelect    = document.getElementById('type_select');     // NUEVO campo tipo (producto/servicio)
  const productSelect = document.getElementById('product_id');
  const priceInput    = document.getElementById('price');
  const taxSelect     = document.getElementById('tax_rate');        // NUEVO campo impuesto por línea
  const qtyInput      = document.getElementById('quantity');
  const addBtn        = document.getElementById('add-product');
  const tableBody     = document.getElementById('product-table').querySelector('tbody');
  const subtotalDOM   = document.getElementById('subtotal');
  const taxAmtDOM     = document.getElementById('tax-amount');
  const totalDOM      = document.getElementById('total');
  const form          = document.getElementById('sale-form');
  const submitBtn     = form.querySelector('button[type="submit"]');

  // Para los combos de cliente/empresa (si los usas en edit también)
  let firstLoad = true;

  // Clientes/Empresas
  function rebuildClients() {
    clientSelect.innerHTML = '<option value="">Seleccionar cliente</option>';
    const list = typeSelect.value === 'company' ? companies : clients;
    list.forEach(item => {
      const opt = new Option(
        item.name + (typeSelect.value === 'company' ? ' (Empresa)' : ''),
        item.id
      );
      if (firstLoad && item.id === initialId) opt.selected = true;
      clientSelect.add(opt);
    });
    firstLoad = false;
  }

  // --- CATÁLOGO DINÁMICO ---
  function fillProductCatalog() {
    productSelect.innerHTML = '<option value="">Seleccionar...</option>';
    allProducts.forEach(p => {
      if (p.type === typeSelect.value) {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.text  = p.name + ' ($' + Number(p.price).toFixed(2) + ')';
        opt.setAttribute('data-price', p.price);
        productSelect.appendChild(opt);
      }
    });
    priceInput.value = '';
  }

  if (typeSelect) typeSelect.addEventListener('change', fillProductCatalog);

  // Sugerir precio editable
  if (productSelect) {
    productSelect.addEventListener('change', function() {
      const selected = productSelect.selectedOptions[0];
      priceInput.value = selected && selected.getAttribute('data-price') ? selected.getAttribute('data-price') : '';
    });
  }

  // --- AGREGAR LÍNEA EDITABLE ---
  addBtn.addEventListener('click', () => {
    const pid = productSelect.value;
    const pname = productSelect.options[productSelect.selectedIndex]
      ? productSelect.options[productSelect.selectedIndex].text.split(' ($')[0]
      : '';
    const price = parseFloat(priceInput.value);
    const qty = parseInt(qtyInput.value, 10);
    const taxRate = parseFloat(taxSelect.value);

    if (!pid || isNaN(price) || price < 0 || isNaN(qty) || qty < 1 || isNaN(taxRate)) {
      alert('Completa todos los campos correctamente.');
      return;
    }

    const idx = tableBody.rows.length;
    const lineSubtotal = price * qty;
    const lineTax     = lineSubtotal * (taxRate / 100);
    const lineTotal   = lineSubtotal + lineTax;

    const tr  = tableBody.insertRow();
    tr.setAttribute('data-index', idx);
    tr.innerHTML = `
      <td>${pname}</td>
      <td>$${price.toFixed(2)}</td>
      <td>${qty}</td>
      <td>${taxRate.toFixed(2)}%</td>
      <td>$${lineTotal.toFixed(2)}</td>
      <td>
        <button type="button" class="DeleteBtn remove-product">Eliminar</button>
        <input type="hidden" name="products[${idx}][id]"       value="${pid}">
        <input type="hidden" name="products[${idx}][name]"     value="${pname}">
        <input type="hidden" name="products[${idx}][price]"    value="${price}">
        <input type="hidden" name="products[${idx}][quantity]" value="${qty}">
        <input type="hidden" name="products[${idx}][tax_rate]" value="${taxRate}">
      </td>
    `;
    reindexRows();
    updateTotals();

    productSelect.value = '';
    priceInput.value    = '';
    taxSelect.value     = '0.00';
    qtyInput.value      = 1;
    submitBtn.disabled  = false;
  });

  // Eliminar línea
  tableBody.addEventListener('click', e => {
    if (!e.target.classList.contains('remove-product')) return;
    e.target.closest('tr').remove();
    reindexRows();
    updateTotals();
    submitBtn.disabled = tableBody.rows.length === 0;
  });

  // Reindexar
  function reindexRows() {
    Array.from(tableBody.rows).forEach((row, i) => {
      row.setAttribute('data-index', i);
      row.querySelectorAll('input[type="hidden"]').forEach(input => {
        input.name = input.name.replace(/\[\d+\]/, `[${i}]`);
      });
    });
  }

  // Calcular totales (impuesto por línea)
  function updateTotals() {
    let sub = 0, totalTax = 0;
    tableBody.querySelectorAll('tr').forEach(row => {
      const price   = parseFloat(row.querySelector('input[name*="[price]"]').value);
      const qty     = parseInt(row.querySelector('input[name*="[quantity]"]').value, 10);
      const taxRate = parseFloat(row.querySelector('input[name*="[tax_rate]"]').value) || 0;
      const ls      = price * qty;
      const lt      = ls * (taxRate / 100);
      sub      += ls;
      totalTax += lt;
    });
    const total = sub + totalTax;
    subtotalDOM.textContent = `$${sub.toFixed(2)}`;
    taxAmtDOM.textContent   = `$${totalTax.toFixed(2)}`;
    totalDOM.textContent    = `$${total.toFixed(2)}`;
    form.querySelector('input[name="subtotal"]').value   = sub;
    form.querySelector('input[name="tax_amount"]').value = totalTax;
    form.querySelector('input[name="total"]').value      = total;
  }

  // Inicialización
  if (typeof fillProductCatalog === "function") fillProductCatalog();
  updateTotals();
});
