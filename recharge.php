<?php
// recharge.php
require_once __DIR__ . '/includes/functions.php';

$type = isset($_GET['type']) ? trim($_GET['type']) : 'mobile';
$validTypes = ['mobile', 'dth', 'electricity', 'broadband', 'fastag', 'gas'];
if (!in_array($type, $validTypes)) {
    $type = 'mobile';
}

$pageTitle = 'Giveoraemart – Recharges & Utility Bill Payments';
require_once __DIR__ . '/includes/header.php';

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reqType = $_POST['service_type'] ?? 'mobile';
    $accNum = $_POST['account_number'] ?? '';
    $operator = $_POST['operator'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $txnId = 'GVM' . rand(10000000, 99999999);
    $successMsg = "Payment successful! Your $reqType recharge/bill payment of " . format_price($amount > 0 ? $amount : 199) . " for account/number <strong>" . htmlspecialchars($accNum) . "</strong> ($operator) is processed. Transaction ID: <strong>$txnId</strong>";
}
?>

<style>
.rc-hero {
  background: linear-gradient(135deg, var(--navy) 0%, #153b66 100%);
  color: #fff;
  padding: 48px 0 36px;
}
.rc-nav {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 8px;
  border-bottom: 1px solid rgba(255,255,255,0.15);
  margin-top: 24px;
}
.rc-tab {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 8px;
  background: rgba(255,255,255,0.08);
  color: #cfd9e6;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  white-space: nowrap;
  transition: all 0.2s ease;
}
.rc-tab:hover, .rc-tab.active {
  background: #fff;
  color: var(--navy);
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.rc-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 28px;
  box-shadow: var(--shadow);
  margin-top: 24px;
}
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 18px;
  margin-bottom: 20px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
}
.form-group input, .form-group select {
  padding: 11px 14px;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-family: var(--sans);
  font-size: 15px;
}
.plans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 14px;
  margin-top: 16px;
}
.plan-item {
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 16px;
  background: var(--bg);
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
}
.plan-item:hover, .plan-item.selected {
  border-color: var(--blue);
  background: rgba(26,95,180,0.04);
}
.plan-price {
  font-family: var(--serif);
  font-size: 20px;
  font-weight: 600;
  color: var(--blue);
}
.plan-validity {
  font-size: 12px;
  color: var(--muted);
  margin-top: 2px;
}
.plan-desc {
  font-size: 13px;
  margin-top: 8px;
  color: var(--text);
}
.offer-banner {
  background: linear-gradient(90deg, #1f8a5b 0%, #135e3d 100%);
  color: #fff;
  border-radius: 10px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
}
</style>

<!-- Hero Section -->
<section class="rc-hero">
  <div class="wrap">
    <h1>Recharges & Bill Payments</h1>
    <p style="color:#c5d1e0;margin:6px 0 0;max-width:600px">
      Instant mobile top-ups, DTH renewals, electricity and utility bill payments with Giveoraemart Pay. Earn Cashback points on every payment!
    </p>

    <!-- Service Tabs -->
    <div class="rc-nav">
      <a class="rc-tab <?= $type === 'mobile' ? 'active' : '' ?>" href="/recharge.php?type=mobile">
        <i class="fa-solid fa-mobile-screen-button"></i> Mobile Recharge
      </a>
      <a class="rc-tab <?= $type === 'dth' ? 'active' : '' ?>" href="/recharge.php?type=dth">
        <i class="fa-solid fa-tv"></i> DTH Recharge
      </a>
      <a class="rc-tab <?= $type === 'electricity' ? 'active' : '' ?>" href="/recharge.php?type=electricity">
        <i class="fa-solid fa-bolt"></i> Electricity Bill
      </a>
      <a class="rc-tab <?= $type === 'broadband' ? 'active' : '' ?>" href="/recharge.php?type=broadband">
        <i class="fa-solid fa-wifi"></i> Broadband & Landline
      </a>
      <a class="rc-tab <?= $type === 'fastag' ? 'active' : '' ?>" href="/recharge.php?type=fastag">
        <i class="fa-solid fa-car"></i> FASTag Recharge
      </a>
      <a class="rc-tab <?= $type === 'gas' ? 'active' : '' ?>" href="/recharge.php?type=gas">
        <i class="fa-solid fa-fire-flame-simple"></i> Gas Cylinder
      </a>
    </div>
  </div>
</section>

<section class="s" style="padding-top:28px">
  <div class="wrap">

    <?php if (!empty($successMsg)): ?>
      <div class="panel" style="background:#eefbf3;border:1px solid #1f8a5b;color:#135e3d;margin-bottom:24px;padding:18px 24px">
        <div style="display:flex;align-items:center;gap:12px">
          <i class="fa-solid fa-circle-check" style="font-size:24px;color:#1f8a5b"></i>
          <div>
            <h3 style="margin:0 0 2px;font-size:16px;color:#135e3d">Transaction Successful!</h3>
            <p style="margin:0;font-size:14px"><?= $successMsg ?></p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Promo Offer Banner -->
    <div class="offer-banner">
      <div style="display:flex;align-items:center;gap:12px">
        <i class="fa-solid fa-gift" style="font-size:26px;color:#ffd700"></i>
        <div>
          <strong style="font-size:15px;display:block">Special Recharge Cashback Offer</strong>
          <span style="font-size:13px;opacity:0.9">Get 5% instant cashback up to ₹50 on all utility payments when using Giveoraemart UPI!</span>
        </div>
      </div>
      <span style="background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:99px;font-size:12px;font-weight:600;white-space:nowrap">Code: GVMRECHARGE</span>
    </div>

    <div class="rc-card">
      <form method="POST" action="/recharge.php?type=<?= htmlspecialchars($type) ?>" id="recharge-form">
        <input type="hidden" name="service_type" value="<?= htmlspecialchars($type) ?>">

        <?php if ($type === 'mobile'): ?>
          <h2 style="font-size:1.4rem;margin-bottom:18px">Mobile Prepaid & Postpaid Recharge</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="mobile_num">Mobile Number</label>
              <input type="tel" id="mobile_num" name="account_number" placeholder="Enter 10-digit mobile number" maxlength="10" required pattern="[0-9]{10}">
            </div>
            <div class="form-group">
              <label for="operator">Operator</label>
              <select id="operator" name="operator" required>
                <option value="">Select Telecom Operator</option>
                <option value="Jio Prepaid">Jio Prepaid</option>
                <option value="Airtel Prepaid">Airtel Prepaid</option>
                <option value="Vi (Vodafone Idea)">Vi (Vodafone Idea)</option>
                <option value="BSNL Prepaid">BSNL Prepaid</option>
              </select>
            </div>
            <div class="form-group">
              <label for="circle">Circle / State</label>
              <select id="circle" name="circle">
                <option value="All India">All India</option>
                <option value="Delhi NCR">Delhi NCR</option>
                <option value="Mumbai">Mumbai</option>
                <option value="Maharashtra & Goa">Maharashtra & Goa</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="West Bengal">West Bengal</option>
              </select>
            </div>
            <div class="form-group">
              <label for="amount">Recharge Amount (₹)</label>
              <input type="number" id="amount" name="amount" placeholder="Enter amount or select plan below" min="10" required>
            </div>
          </div>

          <!-- Recommended Mobile Plans -->
          <h3 style="font-size:1.1rem;margin:24px 0 12px">Popular Recharge Plans</h3>
          <div class="plans-grid">
            <div class="plan-item" onclick="selectPlan(299)">
              <div class="plan-price">₹299</div>
              <div class="plan-validity">28 Days Validity · 1.5 GB/Day</div>
              <div class="plan-desc">Truly Unlimited Calls + 100 SMS/day + Free National Roaming.</div>
            </div>
            <div class="plan-item" onclick="selectPlan(666)">
              <div class="plan-price">₹666</div>
              <div class="plan-validity">84 Days Validity · 1.5 GB/Day</div>
              <div class="plan-desc">Truly Unlimited Calls + 100 SMS/day + Disney+ Hotstar Access.</div>
            </div>
            <div class="plan-item" onclick="selectPlan(155)">
              <div class="plan-price">₹155</div>
              <div class="plan-validity">24 Days Validity · 1 GB Total</div>
              <div class="plan-desc">Unlimited voice calls + 300 SMS total. Ideal for secondary SIMs.</div>
            </div>
            <div class="plan-item" onclick="selectPlan(2999)">
              <div class="plan-price">₹2,999</div>
              <div class="plan-validity">365 Days Validity · 2.5 GB/Day</div>
              <div class="plan-desc">Annual Saver Plan! Unlimited Voice + 100 SMS/day + High Speed 5G.</div>
            </div>
          </div>

        <?php elseif ($type === 'dth'): ?>
          <h2 style="font-size:1.4rem;margin-bottom:18px">DTH Subscription Renewal</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="dth_operator">DTH Provider</label>
              <select id="dth_operator" name="operator" required>
                <option value="">Select DTH Operator</option>
                <option value="Tata Play (formerly Tata Sky)">Tata Play</option>
                <option value="Airtel Digital TV">Airtel Digital TV</option>
                <option value="Dish TV">Dish TV</option>
                <option value="Sun Direct">Sun Direct</option>
                <option value="d2h">d2h</option>
              </select>
            </div>
            <div class="form-group">
              <label for="vc_num">Subscriber ID / Smart Card / VC Number</label>
              <input type="text" id="vc_num" name="account_number" placeholder="Enter Subscriber ID or VC number" required>
            </div>
            <div class="form-group">
              <label for="amount">Recharge Amount (₹)</label>
              <input type="number" id="amount" name="amount" placeholder="Enter monthly subscription amount" min="50" required>
            </div>
          </div>

          <h3 style="font-size:1.1rem;margin:24px 0 12px">Suggested Packs</h3>
          <div class="plans-grid">
            <div class="plan-item" onclick="selectPlan(350)">
              <div class="plan-price">₹350</div>
              <div class="plan-validity">1 Month · Family Super Pack</div>
              <div class="plan-desc">180+ Channels including HD Sports, News, Movies & Entertainment.</div>
            </div>
            <div class="plan-item" onclick="selectPlan(999)">
              <div class="plan-price">₹999</div>
              <div class="plan-validity">3 Months · Sports Saver Combo</div>
              <div class="plan-desc">All Star Sports HD, Regional Packs & Kids channels included.</div>
            </div>
            <div class="plan-item" onclick="selectPlan(3600)">
              <div class="plan-price">₹3,600</div>
              <div class="plan-validity">12 Months · Annual Mega Pack</div>
              <div class="plan-desc">1 Full Year HD subscription with 2 Months free service extended.</div>
            </div>
          </div>

        <?php elseif ($type === 'electricity'): ?>
          <h2 style="font-size:1.4rem;margin-bottom:18px">Electricity Bill Payment</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="elec_state">State</label>
              <select id="elec_state">
                <option value="Karnataka">Karnataka</option>
                <option value="Delhi">Delhi</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
              </select>
            </div>
            <div class="form-group">
              <label for="operator">Electricity Board</label>
              <select id="operator" name="operator" required>
                <option value="BESCOM - Bangalore Electricity">BESCOM - Bangalore Electricity</option>
                <option value="BSES Rajdhani - Delhi">BSES Rajdhani - Delhi</option>
                <option value="MSEDCL - Maharashtra">MSEDCL - Maharashtra</option>
                <option value="TANGEDCO - Tamil Nadu">TANGEDCO - Tamil Nadu</option>
                <option value="UPPCL - Uttar Pradesh">UPPCL - Uttar Pradesh</option>
              </select>
            </div>
            <div class="form-group">
              <label for="ca_num">Consumer Account / CA Number</label>
              <input type="text" id="ca_num" name="account_number" placeholder="Enter Consumer Account Number" required>
            </div>
            <div class="form-group">
              <label for="amount">Bill Amount (₹)</label>
              <input type="number" id="amount" name="amount" placeholder="Fetch bill or enter amount" required>
            </div>
          </div>

          <div style="margin-bottom:20px">
            <button type="button" class="btn ghost" style="color:var(--blue)" onclick="fetchSampleBill()">
              <i class="fa-solid fa-magnifying-glass"></i> Fetch Online Bill
            </button>
            <div id="bill-preview" style="display:none;margin-top:14px;padding:16px;background:var(--bg);border-radius:8px;border:1px solid var(--line)">
              <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                  <strong style="display:block;font-size:15px" id="bill-name">Customer: Rajesh Kumar</strong>
                  <span style="font-size:13px;color:var(--muted)">Bill Date: Oct 15, 2023 · Due Date: Nov 05, 2023</span>
                </div>
                <b style="font-size:22px;color:var(--green)" id="bill-amt">₹1,240.00</b>
              </div>
            </div>
          </div>

        <?php elseif ($type === 'broadband'): ?>
          <h2 style="font-size:1.4rem;margin-bottom:18px">Broadband & Landline Bill Payment</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="operator">Service Provider</label>
              <select id="operator" name="operator" required>
                <option value="">Select Broadband Provider</option>
                <option value="Airtel Broadband / Fiber">Airtel Broadband / Fiber</option>
                <option value="JioFiber">JioFiber</option>
                <option value="ACT Fibernet">ACT Fibernet</option>
                <option value="BSNL Broadband">BSNL Broadband</option>
                <option value="Tata Play Fiber">Tata Play Fiber</option>
              </select>
            </div>
            <div class="form-group">
              <label for="bb_num">Landline / Account Number (With Std Code)</label>
              <input type="text" id="bb_num" name="account_number" placeholder="e.g. 08012345678 or Account ID" required>
            </div>
            <div class="form-group">
              <label for="amount">Bill Amount (₹)</label>
              <input type="number" id="amount" name="amount" placeholder="Enter bill amount" required>
            </div>
          </div>

        <?php elseif ($type === 'fastag'): ?>
          <h2 style="font-size:1.4rem;margin-bottom:18px">FASTag Toll Recharge</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="operator">FASTag Issuer Bank / Provider</label>
              <select id="operator" name="operator" required>
                <option value="">Select Issuing Bank</option>
                <option value="NHAI FASTag">NHAI FASTag</option>
                <option value="Paytm FASTag">Paytm FASTag</option>
                <option value="ICICI Bank FASTag">ICICI Bank FASTag</option>
                <option value="SBI FASTag">SBI FASTag</option>
                <option value="HDFC Bank FASTag">HDFC Bank FASTag</option>
                <option value="Axis Bank FASTag">Axis Bank FASTag</option>
              </select>
            </div>
            <div class="form-group">
              <label for="veh_num">Vehicle Registration Number</label>
              <input type="text" id="veh_num" name="account_number" placeholder="e.g. KA01AB1234" style="text-transform:uppercase" required>
            </div>
            <div class="form-group">
              <label for="amount">Top-up Amount (₹)</label>
              <input type="number" id="amount" name="amount" placeholder="Enter amount (Min ₹100)" min="100" required>
            </div>
          </div>

          <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
            <button type="button" class="btn ghost" onclick="selectPlan(200)">+ ₹200</button>
            <button type="button" class="btn ghost" onclick="selectPlan(500)">+ ₹500</button>
            <button type="button" class="btn ghost" onclick="selectPlan(1000)">+ ₹1,000</button>
            <button type="button" class="btn ghost" onclick="selectPlan(2000)">+ ₹2,000</button>
          </div>

        <?php elseif ($type === 'gas'): ?>
          <h2 style="font-size:1.4rem;margin-bottom:18px">Book LPG Gas Cylinder Refill</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="operator">Gas Provider</label>
              <select id="operator" name="operator" required>
                <option value="">Select Gas Provider</option>
                <option value="Indane Gas (Indian Oil)">Indane Gas (Indian Oil)</option>
                <option value="HP Gas (Hindustan Petroleum)">HP Gas (Hindustan Petroleum)</option>
                <option value="Bharat Gas (BPCL)">Bharat Gas (BPCL)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="gas_acc">Registered Mobile No / 17-digit LPG ID</label>
              <input type="text" id="gas_acc" name="account_number" placeholder="Enter mobile number or LPG ID" required>
            </div>
            <div class="form-group">
              <label for="amount">Refill Price (₹)</label>
              <input type="number" id="amount" name="amount" value="1050" required readonly style="background:rgba(0,0,0,0.05)">
            </div>
          </div>
        <?php endif; ?>

        <!-- Payment Mode Selector -->
        <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--line)">
          <label style="font-weight:600;display:block;margin-bottom:10px">Payment Method</label>
          <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
              <input type="radio" name="payment_method" value="UPI" checked> Giveoraemart UPI / QR
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
              <input type="radio" name="payment_method" value="WALLET"> Member Matrix Wallet
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
              <input type="radio" name="payment_method" value="CARD"> Debit / Credit Card
            </label>
          </div>

          <button type="submit" class="btn" style="padding:14px 28px;font-size:16px">
            <i class="fa-solid fa-lock" style="margin-right:8px"></i> Proceed to Pay Securely
          </button>
        </div>

      </form>
    </div>

    <!-- Features & Guarantee Grid -->
    <div style="margin-top:48px" class="cards">
      <div class="card">
        <div class="ic"><i class="fa-solid fa-bolt-lightning"></i></div>
        <h3>Instant Processing</h3>
        <p>Direct API integration with all national operators ensures immediate credit to your account.</p>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-shield-halved"></i></div>
        <h3>100% Secure Checkout</h3>
        <p>Bank-grade encryption protects your payment transactions and personal details.</p>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-coins"></i></div>
        <h3>Cashback Rewards</h3>
        <p>Earn reward wallet credits on every successful recharge and bill transaction.</p>
      </div>
    </div>

  </div>
</section>

<script>
function selectPlan(amt) {
  var amtInput = document.getElementById('amount');
  if (amtInput) {
    amtInput.value = amt;
    amtInput.focus();
  }
}

function fetchSampleBill() {
  var preview = document.getElementById('bill-preview');
  var amtInput = document.getElementById('amount');
  if (preview) {
    preview.style.display = 'block';
  }
  if (amtInput) {
    amtInput.value = 1240;
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
