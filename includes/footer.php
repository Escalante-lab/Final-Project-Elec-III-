</main>
<footer class="text-center text-muted py-4 small">
  &copy; <?= date('Y') ?> Meat Inventory &amp; Ordering Management System
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.alert-success, .alert-danger').forEach(a => {
    if (a.textContent.trim()) a.classList.add('auto-show');
  });
</script>
</body>
</html>