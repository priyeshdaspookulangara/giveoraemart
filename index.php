<?php
// index.php
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();

// Fetch Packages / Combo Bundles
$packageStmt = $pdo->query("SELECT * FROM packages ORDER BY id DESC LIMIT 3");
$packages = $packageStmt->fetchAll();

// Fetch Daily Repurchase Essentials
$repurchaseStmt = $pdo->query("SELECT * FROM products WHERE purchase_type = 'REPURCHASE' ORDER BY id DESC LIMIT 8");
$repurchaseProducts = $repurchaseStmt->fetchAll();

// Fallback if no repurchase products exist
if (empty($repurchaseProducts)) {
    $repurchaseStmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
    $repurchaseProducts = $repurchaseStmt->fetchAll();
}

$pageTitle = "Giveoraemart – Groceries, Bundles, Recharges & Welfare Schemes";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <h1>Quality groceries, utility recharges and community welfare cover.</h1>
      <p>Shop monthly pantry staples and household kits, pay mobile & electricity bills instantly, earn referral rewards, and access health & education insurance through our welfare schemes.</p>
      <div class="cta">
        <a class="btn" href="/shop.php">Explore marketplace</a>
        <a class="btn ghost" href="/recharge.php">Pay bills & recharges</a>
        <a class="btn ghost" href="/welfare-schemes.php">View welfare schemes</a>
      </div>
    </div>
    <aside class="panel" aria-label="Featured offers">
      <h3>Pay instantly with UPI</h3>
      <p>Scan QR code with GPay, PhonePe or Paytm & earn cashback points.</p>
      <div class="item">
        <div>
          <strong>Mobile & DTH Recharge</strong>
          <span>Instant top-up for all operators</span>
        </div>
        <span class="badge" style="background:rgba(26,95,180,0.12);color:var(--blue)">5% Cashback</span>
      </div>
      <div class="item">
        <div>
          <strong>Vidya Vikas & Charity scheme</strong>
          <span>Health, education & family cover</span>
        </div>
        <span class="badge">Active Cover</span>
      </div>
    </aside>
  </div>
</section>

<!-- Facts Bar -->
<div class="facts">
  <div class="wrap">
    <div class="fact"><b>10K+</b><span>Active members</span></div>
    <div class="fact"><b>₹60K</b><span>Charity support per user</span></div>
    <div class="fact"><b>₹10 Lakh</b><span>Maximum health cover</span></div>
    <div class="fact"><b>100%</b><span>Instant bill confirmation</span></div>
  </div>
</div>

<!-- Recharges & Utility Services Showcase Section -->
<section class="s" style="padding-bottom:0">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Recharges & Utility Services</h2>
        <p>Instant mobile top-ups, TV renewals, electricity bills, and toll payments right from your Giveoraemart account.</p>
      </div>
      <a class="btn ghost" style="color:var(--blue)" href="/recharge.php">Explore all services</a>
    </div>

    <div class="cards" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
      <div class="card" style="align-items:center;text-align:center">
        <div class="ic" style="font-size:22px"><i class="fa-solid fa-mobile-screen-button"></i></div>
        <h3>Mobile Recharge</h3>
        <p style="font-size:13px">Prepaid & Postpaid plans for Jio, Airtel, Vi, BSNL</p>
        <a href="/recharge.php?type=mobile">Recharge now &rarr;</a>
      </div>
      <div class="card" style="align-items:center;text-align:center">
        <div class="ic" style="font-size:22px"><i class="fa-solid fa-tv"></i></div>
        <h3>DTH Renewal</h3>
        <p style="font-size:13px">Tata Play, Dish TV, Airtel Digital & Sun Direct</p>
        <a href="/recharge.php?type=dth">Renew pack &rarr;</a>
      </div>
      <div class="card" style="align-items:center;text-align:center">
        <div class="ic" style="font-size:22px"><i class="fa-solid fa-bolt"></i></div>
        <h3>Electricity Bill</h3>
        <p style="font-size:13px">Pay state electricity board bills online instantly</p>
        <a href="/recharge.php?type=electricity">Pay bill &rarr;</a>
      </div>
      <div class="card" style="align-items:center;text-align:center">
        <div class="ic" style="font-size:22px"><i class="fa-solid fa-wifi"></i></div>
        <h3>Broadband & Landline</h3>
        <p style="font-size:13px">Airtel Fiber, JioFiber, ACT & BSNL Internet</p>
        <a href="/recharge.php?type=broadband">Pay broadband &rarr;</a>
      </div>
      <div class="card" style="align-items:center;text-align:center">
        <div class="ic" style="font-size:22px"><i class="fa-solid fa-car"></i></div>
        <h3>FASTag Recharge</h3>
        <p style="font-size:13px">Top up NHAI, Paytm, ICICI, SBI & HDFC FASTag</p>
        <a href="/recharge.php?type=fastag">Top up FASTag &rarr;</a>
      </div>
      <div class="card" style="align-items:center;text-align:center">
        <div class="ic" style="font-size:22px"><i class="fa-solid fa-fire-flame-simple"></i></div>
        <h3>Gas Refill</h3>
        <p style="font-size:13px">Book Indane, HP & Bharat Gas refills online</p>
        <a href="/recharge.php?type=gas">Book cylinder &rarr;</a>
      </div>
    </div>
  </div>
</section>

<!-- Featured Bundles Section -->
<section class="s" style="padding-bottom:0">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Featured combo packages</h2>
        <p>Ready-made packs at a lower price than buying each item on its own.</p>
      </div>
      <a class="btn ghost" style="color:var(--blue)" href="/shop.php?type=package">See all bundles</a>
    </div>

    <?php if (!empty($packages)): ?>
      <div class="bundles">
        <?php foreach ($packages as $index => $pkg): ?>
          <article class="bundle <?= $index === 0 ? 'lead' : '' ?>">
            <?php if ($index === 0): ?>
              <span class="badge">Best value</span>
            <?php endif; ?>
            <h3><?= htmlspecialchars($pkg['name']) ?></h3>
            <p><?= htmlspecialchars($pkg['description']) ?></p>

            <?php
            // Fetch items for this package
            $itemStmt = $pdo->prepare("SELECT pi.*, p.name as product_name FROM package_items pi LEFT JOIN products p ON pi.product_id = p.id WHERE pi.package_id = ?");
            $itemStmt->execute([$pkg['id']]);
            $pkgItems = $itemStmt->fetchAll();
            ?>

            <?php if (!empty($pkgItems)): ?>
              <ul>
                <?php foreach (array_slice($pkgItems, 0, 4) as $pi): ?>
                  <li><?= htmlspecialchars($pi['product_name'] ?? $pi['item_name']) ?><span>x<?= $pi['quantity'] ?></span></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <div class="price">
              <div>
                <b><?= format_price($pkg['discount_price'] > 0 ? $pkg['discount_price'] : $pkg['price']) ?></b>
                <?php if ($pkg['discount_price'] > 0): ?>
                  <s><?= format_price($pkg['price']) ?></s>
                  <div class="save">You save <?= format_price($pkg['price'] - $pkg['discount_price']) ?></div>
                <?php endif; ?>
              </div>
              <button onclick="addToCart('PACKAGE', <?= $pkg['id'] ?>, 1)" class="add" type="button">Add to cart</button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="bundles">
        <article class="bundle lead">
          <span class="badge">Best value</span>
          <h3>Family Pantry Bundle</h3>
          <p>A month of kitchen staples for a household of four.</p>
          <ul>
            <li>Rice<span>5 kg</span></li>
            <li>Sunflower oil<span>1 L</span></li>
            <li>Tea<span>500 g</span></li>
            <li>Spice set<span>6 packs</span></li>
          </ul>
          <div class="price"><div><b>$49.99</b><s>$62.00</s><div class="save">You save $12.01</div></div><button onclick="addToCart('PACKAGE', 1, 1)" class="add" type="button">Add to cart</button></div>
        </article>
        <article class="bundle">
          <h3>Wellness Care Bundle</h3>
          <p>Daily personal care essentials.</p>
          <ul>
            <li>Face cream<span>1 pc</span></li>
            <li>Herbal soap<span>3 pcs</span></li>
            <li>Hair oil<span>200 ml</span></li>
          </ul>
          <div class="price"><div><b>$24.99</b><s>$29.50</s><div class="save">You save $4.51</div></div><button onclick="addToCart('PACKAGE', 2, 1)" class="add" type="button">Add to cart</button></div>
        </article>
        <article class="bundle">
          <h3>Kitchen Starter Bundle</h3>
          <p>Cookware basics for a new home.</p>
          <ul>
            <li>Non-stick pan<span>1 pc</span></li>
            <li>Pressure cooker<span>3 L</span></li>
            <li>Utensil set<span>5 pcs</span></li>
          </ul>
          <div class="price"><div><b>$34.99</b><s>$42.00</s><div class="save">You save $7.01</div></div><button onclick="addToCart('PACKAGE', 3, 1)" class="add" type="button">Add to cart</button></div>
        </article>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- Services & Ecosystem Section -->
<section class="s">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Platform Services & Member Ecosystem</h2>
        <p>Groceries, bill payments, UPI checkout and a referral wallet integrated in one account.</p>
      </div>
    </div>
    <div class="cards">
      <div class="card">
        <div class="ic"><i class="fa-solid fa-cart-shopping"></i></div>
        <h3>Repurchase grocery</h3>
        <p>Monthly rice, oils and tea with home delivery and order tracking.</p>
        <a href="/shop.php?purchase_type=REPURCHASE">Shop repurchase</a>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-receipt"></i></div>
        <h3>Utility & Bill Pay</h3>
        <p>Instant mobile, DTH, electricity, broadband and FASTag payments.</p>
        <a href="/recharge.php">Pay bills now</a>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-qrcode"></i></div>
        <h3>Instant UPI payment</h3>
        <p>Checkout by QR code and confirm with your UTR number.</p>
        <a href="/checkout.php">Checkout details</a>
      </div>
      <div class="card">
        <div class="ic"><i class="fa-solid fa-sitemap"></i></div>
        <h3>Matrix referral wallet</h3>
        <p>Auto-spillover across 11 levels with direct income and reserve tracking.</p>
        <a href="/account.php">Open my wallet</a>
      </div>
    </div>
  </div>
</section>

<!-- Welfare Schemes Showcase Section -->
<section class="s welfare">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Welfare schemes & Community support</h2>
        <p>Financial support, health insurance and education cover for active members and their families.</p>
      </div>
      <a class="btn" style="background:#fff;color:var(--navy);border-color:#fff" href="/welfare-schemes.php">All scheme details</a>
    </div>
    <div class="split">
      <article class="scheme">
        <span class="tag">Support tier from ₹10,000 · 60 payout cycles</span>
        <h3>Charity Support Scheme</h3>
        <p>Ongoing financial relief and medical protection for member families.</p>
        <ul>
          <li><strong>₹3,000 × 60 cycles</strong> guaranteed monthly support payout</li>
          <li>EMI assistance for hospital and medical loans over ₹50,000</li>
          <li><strong>₹10 Lakh</strong> cover for female members, <strong>₹5 Lakh</strong> for males</li>
        </ul>
      </article>
      <article class="scheme">
        <span class="tag">Youth & Student Protection</span>
        <h3>Vidya Vikas Scheme</h3>
        <p>Education assistance, student medical cover and accident protection for parents.</p>
        <ul>
          <li><strong>₹2 Lakh</strong> health insurance for active enrolled students</li>
          <li><strong>₹50,000</strong> accidental death cover for students</li>
          <li><strong>₹4 Lakh</strong> accidental death cover for parent policyholder</li>
        </ul>
      </article>
    </div>
  </div>
</section>

<!-- Daily Repurchase Essentials Section -->
<section class="s">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Daily repurchase essentials</h2>
      </div>
      <a class="btn ghost" style="color:var(--blue)" href="/shop.php?purchase_type=REPURCHASE">Browse all</a>
    </div>

    <?php if (!empty($repurchaseProducts)): ?>
      <div class="grid">
        <?php foreach ($repurchaseProducts as $prod): ?>
          <article class="prod" data-name="<?= htmlspecialchars(strtolower($prod['name'])) ?>">
            <div class="shot">
              <?php if (!empty($prod['image_url'])): ?>
                <img src="<?= htmlspecialchars($prod['image_url']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" style="width:100%;height:100%;object-fit:cover;">
              <?php else: ?>
                <span><?= htmlspecialchars($prod['name']) ?></span>
              <?php endif; ?>
            </div>
            <div class="pbody">
              <small><?= htmlspecialchars($prod['purchase_type'] ?? 'Essential') ?></small>
              <h3><a href="/product.php?id=<?= $prod['id'] ?>" style="text-decoration:none"><?= htmlspecialchars($prod['name']) ?></a></h3>
              <div class="buy">
                <b><?= format_price($prod['price']) ?></b>
                <button onclick="addToCart('PRODUCT', <?= $prod['id'] ?>, 1)" class="add" type="button">Add to cart</button>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="grid">
        <article class="prod" data-name="face cream wellness">
          <div class="shot">Product image</div>
          <div class="pbody">
            <small>Wellness</small>
            <h3><a href="/product.php?id=2" style="text-decoration:none">Face cream</a></h3>
            <div class="buy"><b>$1.27</b><button onclick="addToCart('PRODUCT', 2, 1)" class="add" type="button">Add to cart</button></div>
          </div>
        </article>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
