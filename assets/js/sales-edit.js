document.addEventListener('DOMContentLoaded', () => {
  const typeSelect   = document.getElementById('client_type');
  const clientSelect = document.getElementById('client_id');
  const productSelect= document.getElementById('product_id');
  const qtyInput     = document.getElementById('quantity');
  const addBtn       = document.getElementById('add-product');
  const tableBody    = document.getElementById('product-table').querySelector('tbody');
  const subtotalDOM  = document.getElementById('subtotal');
  const taxAmtDOM    = document.getElementById('tax-amount');
  const totalDOM     = document.getElementById('total');
  const form         = document.getElementById('sale-form');
  const submitBtn    = form.querySelector('button[type="submit"]');

  let firstLoad = true;

  // 1) Rellenar clientes/empresas
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

  // 2) Reindexar tabla
  function reindexRows() {
    Array.from(tableBody.rows).forEach((row, i) => {
      row.setAttribute('data-index', i);
      row.querySelectorAll('input[type="hidden"]').forEach(input => {
        input.name = input.name.replace(/\[\d+\]/, `[${i}]`);
      });
    });
  }

  // 3) Actualizar totales
  function updateTotals() {
    let sub = 0;
    tableBody.querySelectorAll('tr').forEach(row => {
      const price = parseFloat(row.querySelector('input[name*="[price]"]').value);
      const qty   = parseInt(row.querySelector('input[name*="[quantity]"]').value, 10);
      sub += price * qty;
    });
    const taxPerc = parseFloat(form.querySelector('input[name="tax_percentage"]').value) || 0;
    const taxAmt  = sub * (taxPerc / 100);
    const total   = sub + taxAmt;

    subtotalDOM.textContent = `$${sub.toFixed(2)}`;
    taxAmtDOM.textContent   = `$${taxAmt.toFixed(2)}`;
    totalDOM.textContent    = `$${total.toFixed(2)}`;

    form.querySelector('input[name="subtotal"]').value   = sub;
    form.querySelector('input[name="tax_amount"]').value = taxAmt;
    form.querySelector('input[name="total"]').value      = total;

    submitBtn.disabled = tableBody.rows.length === 0;
  }

  // 4) Eventos
  typeSelect.addEventListener('change', rebuildClients);

  addBtn.addEventListener('click', () => {
    const pid = productSelect.value;
    const qty = parseInt(qtyInput.value, 10);

    // Validación estricta
    if (!pid) {
      alert('Por favor selecciona un producto');
      return;
    }
    if (isNaN(qty) || qty < 1) {
      alert('Por favor ingresa una cantidad válida');
      return;
    }

    const pname = productSelect.options[productSelect.selectedIndex].text.split(' ($')[0];
    const price = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);

    const idx = tableBody.rows.length;
    const tr  = tableBody.insertRow();
    tr.setAttribute('data-index', idx);
    tr.innerHTML = `
      <td>${pname}</td>
      <td>$${price.toFixed(2)}</td>
      <td>${qty}</td>
      <td>$${(price*qty).toFixed(2)}</td>
      <td>
        <button type="button" class="DeleteBtn remove-product">Eliminar</button>
        <input type="hidden" name="products[${idx}][id]"       value="${pid}">
        <input type="hidden" name="products[${idx}][name]"     value="${pname}">
        <input type="hidden" name="products[${idx}][price]"    value="${price}">
        <input type="hidden" name="products[${idx}][quantity]" value="${qty}">
      </td>
    `;
    reindexRows();
    updateTotals();

    productSelect.value = '';
    qtyInput.value      = 1;
  });

  tableBody.addEventListener('click', e => {
    if (!e.target.classList.contains('remove-product')) return;
    e.target.closest('tr').remove();
    reindexRows();
    updateTotals();
  });

  // 5) Inicialización
  rebuildClients();
  updateTotals();
});
