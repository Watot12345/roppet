      </div> <!-- /admin-content -->
    </main> <!-- /admin-main -->
  </div> <!-- /admin-wrapper -->

  <!-- Global Admin Loading Screen -->
  <div id="roppetsLoader" aria-hidden="true" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(22, 18, 29, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: #ffffff; padding: 28px 36px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); text-align: center; min-width: 220px;">
      <div style="width: 44px; height: 44px; border: 4px solid #f3e8f0; border-top-color: #852e70; border-radius: 50%; margin: 0 auto 14px auto; animation: roppetSpin 0.75s linear infinite;"></div>
      <div id="roppetsLoaderText" style="font-weight: 600; font-size: 14px; color: #1e293b;">Loading, please wait...</div>
    </div>
  </div>
  <style>
    @keyframes roppetSpin { to { transform: rotate(360deg); } }
  </style>
  <script>
    (function() {
      var loader = document.getElementById('roppetsLoader');
      var loaderText = document.getElementById('roppetsLoaderText');
      window.showRoppetsLoader = function(msg) {
        if (!loader) return;
        if (loaderText) loaderText.textContent = msg || 'Loading, please wait...';
        loader.style.display = 'flex';
      };
      window.hideRoppetsLoader = function() {
        if (loader) loader.style.display = 'none';
      };

      window.addEventListener('pageshow', function() {
        window.hideRoppetsLoader();
      });

      document.addEventListener('click', function(e) {
        if (e.defaultPrevented || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        var link = e.target.closest('a');
        if (!link) return;
        var href = link.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
        if (link.target === '_blank' || link.hasAttribute('download') || href.indexOf('export.php') !== -1) return;
        window.showRoppetsLoader('Loading page...');
      });

      document.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        window.showRoppetsLoader('Processing request...');
      });
    })();
  </script>
</body>
</html>
