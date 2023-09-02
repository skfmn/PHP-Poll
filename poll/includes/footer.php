<!-- Footer -->
    <footer id="footer">
        <div class="copyright">
            <a href="http://www.phpjunction.com/webapps/">PHP Poll</a> Copyright &copy; <?php echo date("Y"); ?> <a href="http://www.phpjunction.com">PHP junction</a>
        </div>
    </footer>

  <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="../assets/js/jquery.fancybox.js"></script>
    <script src="../assets/js/skel.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/jsfunctions.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
          $(".iframe").fancybox();
          $(".picimg").fancybox();
          $("#textmsg").fancybox();
          $("#textmsg").trigger('click');
      });
    </script>
</body>
</html>