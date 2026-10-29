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
          <li><a href="/login.php" onclick="openAuthModal('signin'); return false;">Login or sign up</a></li>
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

<!-- Auth Modal Dialog -->
<div id="auth-modal" class="auth-modal-overlay" style="display:none;" onclick="if(event.target===this)closeAuthModal()">
  <div class="auth-modal-box">
    <button type="button" class="auth-modal-close" onclick="closeAuthModal()" aria-label="Close dialog">&times;</button>
    <div class="auth-tabs">
      <button type="button" id="tab-signin-btn" class="auth-tab active" onclick="switchAuthTab('signin')">Sign In</button>
      <button type="button" id="tab-signup-btn" class="auth-tab" onclick="switchAuthTab('signup')">Sign Up</button>
    </div>

    <div id="auth-modal-error" class="auth-error" style="display:none;"></div>

    <!-- Sign In Form -->
    <form id="form-signin" class="auth-form" onsubmit="handleAuthSubmit(event, 'login')">
      <div class="auth-field">
        <label for="signin-email">Email Address</label>
        <input type="email" id="signin-email" name="email" required placeholder="john@example.com" value="john@example.com">
      </div>
      <div class="auth-field">
        <label for="signin-password">Password</label>
        <input type="password" id="signin-password" name="password" required placeholder="••••••••" value="customer123">
      </div>
      <button type="submit" class="btn auth-submit-btn">Sign In</button>
      <p class="auth-switch-text">Don't have an account? <a href="#" onclick="switchAuthTab('signup'); return false;">Register Now</a></p>
    </form>

    <!-- Sign Up Form -->
    <form id="form-signup" class="auth-form" style="display:none;" onsubmit="handleAuthSubmit(event, 'register')">
      <div class="auth-field">
        <label for="signup-name">Full Name *</label>
        <input type="text" id="signup-name" name="name" required placeholder="John Doe">
      </div>
      <div class="auth-field">
        <label for="signup-email">Email Address *</label>
        <input type="email" id="signup-email" name="email" required placeholder="john@example.com">
      </div>
      <div class="auth-field">
        <label for="signup-phone">Phone Number</label>
        <input type="tel" id="signup-phone" name="phone" placeholder="+1 (555) 000-1234">
      </div>
      <div class="auth-field">
        <label for="signup-sponsor">Sponsor / Referrer ID (Optional)</label>
        <input type="number" id="signup-sponsor" name="sponsor_id" placeholder="e.g. 2">
      </div>
      <div class="auth-field">
        <label for="signup-password">Password *</label>
        <input type="password" id="signup-password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn auth-submit-btn">Create Account</button>
      <p class="auth-switch-text">Already have an account? <a href="#" onclick="switchAuthTab('signin'); return false;">Sign In</a></p>
    </form>
  </div>
</div>

<style>
.auth-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(12, 35, 64, 0.75);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.auth-modal-box {
  position: relative;
  width: 100%;
  max-width: 440px;
  background: var(--card);
  color: var(--text);
  border: 12px solid rgba(255, 255, 255, 0.25);
  background-clip: padding-box;
  border-radius: 28px;
  padding: 32px 28px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  animation: modalPop 0.2s ease-out;
}
@media (prefers-color-scheme: dark) {
  .auth-modal-box {
    border-color: rgba(255, 255, 255, 0.18);
  }
}
@keyframes modalPop {
  from { opacity: 0; transform: scale(0.94) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.auth-modal-close {
  position: absolute;
  top: 16px;
  right: 18px;
  background: transparent;
  border: 0;
  color: var(--muted);
  font-size: 28px;
  line-height: 1;
  cursor: pointer;
}
.auth-modal-close:hover { color: var(--text); }
.auth-tabs {
  display: flex;
  gap: 8px;
  border-bottom: 2px solid var(--line);
  margin-bottom: 24px;
  padding-bottom: 8px;
}
.auth-tab {
  flex: 1;
  background: transparent;
  border: 0;
  padding: 10px 0;
  font: 600 16px var(--sans);
  color: var(--muted);
  cursor: pointer;
  border-bottom: 3px solid transparent;
  margin-bottom: -10px;
  transition: all 0.15s;
}
.auth-tab.active {
  color: var(--blue);
  border-bottom-color: var(--blue);
}
.auth-error {
  background: rgba(225, 29, 72, 0.1);
  border: 1px solid rgba(225, 29, 72, 0.3);
  color: #f43f5e;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 18px;
}
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.auth-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.auth-field label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--muted);
}
.auth-field input {
  width: 100%;
  border: 1px solid var(--line);
  background: var(--bg);
  color: var(--text);
  padding: 11px 14px;
  border-radius: 10px;
  font: 400 14px var(--sans);
  outline: none;
}
.auth-field input:focus {
  border-color: var(--blue);
}
.auth-submit-btn {
  width: 100%;
  padding: 12px;
  margin-top: 6px;
  border-radius: 10px;
  font-weight: 600;
}
.auth-switch-text {
  text-align: center;
  font-size: 13px;
  color: var(--muted);
  margin: 6px 0 0;
}
.auth-switch-text a {
  color: var(--blue);
  font-weight: 600;
  text-decoration: none;
}
.auth-switch-text a:hover {
  text-decoration: underline;
}
</style>

<script>
function openAuthModal(mode) {
  var modal = document.getElementById('auth-modal');
  if (modal) {
    modal.style.display = 'flex';
    switchAuthTab(mode || 'signin');
  }
}
function closeAuthModal() {
  var modal = document.getElementById('auth-modal');
  if (modal) {
    modal.style.display = 'none';
  }
}
function switchAuthTab(mode) {
  var err = document.getElementById('auth-modal-error');
  if (err) err.style.display = 'none';

  var tabSignin = document.getElementById('tab-signin-btn');
  var tabSignup = document.getElementById('tab-signup-btn');
  var formSignin = document.getElementById('form-signin');
  var formSignup = document.getElementById('form-signup');

  if (mode === 'signup') {
    tabSignin.classList.remove('active');
    tabSignup.classList.add('active');
    formSignin.style.display = 'none';
    formSignup.style.display = 'flex';
  } else {
    tabSignup.classList.remove('active');
    tabSignin.classList.add('active');
    formSignup.style.display = 'none';
    formSignin.style.display = 'flex';
  }
}
function handleAuthSubmit(event, action) {
  event.preventDefault();
  var form = event.target;
  var formData = new FormData(form);
  formData.append('action', action);

  var err = document.getElementById('auth-modal-error');
  if (err) err.style.display = 'none';

  fetch('/api/auth.php', {
    method: 'POST',
    body: formData
  })
  .then(function(res) { return res.json(); })
  .then(function(data) {
    if (data.success) {
      window.location.href = data.redirect || '/account.php';
    } else {
      if (err) {
        err.textContent = data.error || 'An error occurred.';
        err.style.display = 'block';
      }
    }
  })
  .catch(function() {
    if (err) {
      err.textContent = 'Server connection failed.';
      err.style.display = 'block';
    }
  });
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeAuthModal();
});
</script>

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
