</main>

<footer>
  <div class="wrap">
    <div class="fgrid">
      <div>
        <h4>Giveoraemart</h4>
        <p>Everyday groceries and household kits, quality checked and delivered to your door.</p>
      </div>
      <div>
        <h4>Shop</h4>
        <ul>
          <li><a href="/shop.php">Full catalog</a></li>
          <li><a href="/shop.php?purchase_type=REPURCHASE">Repurchase essentials</a></li>
          <li><a href="/shop.php?type=package">Combo packages</a></li>
          <li><a href="/cart.php">My cart</a></li>
        </ul>
      </div>
      <div>
        <h4>Account</h4>
        <ul>
          <li><a href="/login.php">Login or sign up</a></li>
          <li><a href="/account.php">Orders and tracking</a></li>
          <li><a href="/wishlist.php">Wishlist</a></li>
          <li><a href="/admin/login.php">Admin portal</a></li>
        </ul>
      </div>
      <div>
        <h4>Bundle alerts</h4>
        <form onsubmit="return false">
          <input type="email" placeholder="Your email" aria-label="Email">
          <button class="btn" type="submit">Join</button>
        </form>
      </div>
    </div>
    <div class="fine">
      <span>© <?= date('Y') ?> Giveoraemart. All rights reserved.</span>
      <span>Secure SSL checkout · Fast delivery</span>
    </div>
  </div>
</footer>

<script>
(function(){
  var mb=document.getElementById('mm-btn'),mg=document.getElementById('mega');
  if(mb && mg){
    function setM(o){mg.hidden=!o;mb.setAttribute('aria-expanded',String(o))}
    mb.addEventListener('click',function(e){e.stopPropagation();setM(mg.hidden)});
    document.addEventListener('click',function(e){if(!mg.hidden&&!mg.contains(e.target))setM(false)});
    document.addEventListener('keydown',function(e){if(e.key==='Escape'&&!mg.hidden){setM(false);mb.focus()}});
    if(window.matchMedia('(hover:hover) and (min-width:861px)').matches){
      mb.addEventListener('mouseenter',function(){setM(true)});
      var hdr = document.querySelector('header');
      if(hdr){ hdr.addEventListener('mouseleave',function(){setM(false)}); }
    }
  }
  var q=document.getElementById('q');
  if(q){
    q.addEventListener('input',function(){
      var v=q.value.toLowerCase().trim();
      document.querySelectorAll('.prod').forEach(function(p){
        var name = p.getAttribute('data-name') || '';
        p.style.display=(!v||name.toLowerCase().indexOf(v)>-1)?'':'none';
      });
    });
  }
})();
</script>
<script src="/assets/js/app.js"></script>
</body>
</html>
