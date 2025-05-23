</main>

<footer>
    <div class="container">
        <!-- Aquí puedes agregar información de pie de página, copyright, etc. -->
    </div>
</footer>

<!-- Carga el JS principal global del sistema -->
<script src="/swift_invoice/assets/js/main.js"></script>

<!-- Si la vista define $custom_js, lo carga aquí (por ejemplo, para JS exclusivo de ventas o clientes) -->
<?php if (isset($custom_js)): ?>
    <script src="<?php echo $custom_js; ?>"></script>
<?php endif; ?>

</body>
</html>
