    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Swift Invoice. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="/swift_invoice/assets/js/main.js"></script>
    <?php if (isset($custom_js)): ?>
        <script src="<?php echo $custom_js; ?>"></script>
    <?php endif; ?>
</body>
</html>