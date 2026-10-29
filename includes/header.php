<?php
// includes/header.php
require_once __DIR__ . '/functions.php';

$cartDetails = get_cart_details();
$cartCount = $cartDetails['count'];
$currentUser = get_logged_in_user();

$pdo = get_db_connection();
$navCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= htmlspecialchars($pageTitle ?? 'Giveoraemart – Groceries, Bundles & Welfare Schemes') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Serif:wght@500;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root{
  --navy:#0c2340;--blue:#1a5fb4;--blue-d:#134a8e;--green:#1f8a5b;--gold:#b7852b;
  --bg:#f5f7fa;--card:#fff;--text:#1d2733;--muted:#5b6878;--line:#dde3ea;--hero:#0c2340;--hero-text:#fff;
  --shadow:0 1px 2px rgba(12,35,64,.06),0 6px 20px rgba(12,35,64,.06);
  --sans:'IBM Plex Sans',system-ui,sans-serif;--serif:'IBM Plex Serif',Georgia,serif;
  box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)
}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#0d141c;--card:#16202c;--text:#e6ebf1;--muted:#98a5b5;--line:#2a3646;--blue:#4c8fe0;--blue-d:#6aa5ec;--hero:#0a1c33;--shadow:0 1px 2px rgba(0,0,0,.4)}}
:root[data-theme="dark"]{--bg:#0d141c;--card:#16202c;--text:#e6ebf1;--muted:#98a5b5;--line:#2a3646;--blue:#4c8fe0;--blue-d:#6aa5ec;--hero:#0a1c33;--shadow:0 1px 2px rgba(0,0,0,.4)}
html{scroll-padding-top:env(safe-area-inset-top,0px)}
*,*::before,*::after{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:var(--sans);line-height:1.6;overflow-x:hidden;min-height:100vh;display:flex;flex-direction:column}
a{color:inherit}
:focus-visible{outline:3px solid var(--blue);outline-offset:2px}
.wrap{max-width:1180px;margin:0 auto;padding:0 24px;width:100%}
h1,h2,h3{font-family:var(--serif);font-weight:600;line-height:1.2;margin:0;letter-spacing:-.01em}
.btn{display:inline-flex;align-items:center;justify-content:center;font:600 15px var(--sans);padding:12px 22px;border-radius:6px;border:1px solid var(--blue);background:var(--blue);color:#fff;text-decoration:none;cursor:pointer;transition:background .15s}
.btn:hover{background:var(--blue-d)}
.btn.ghost{background:transparent;color:inherit;border-color:currentColor}
.btn.ghost:hover{background:rgba(255,255,255,.1)}
.top{background:var(--navy);color:#cfd9e6;font-size:13px}
.top .wrap{display:flex;justify-content:space-between;gap:16px;padding-top:8px;padding-bottom:8px;flex-wrap:wrap}
header{position:sticky;top:env(safe-area-inset-top,0px);z-index:20;background:var(--card);border-bottom:1px solid var(--line)}
body::before{content:"";position:fixed;inset:0 0 auto;height:env(safe-area-inset-top,0px);background:var(--card);z-index:30}
.nav{display:flex;align-items:center;gap:20px;padding:14px 24px;flex-wrap:wrap}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none;font:600 21px var(--serif);color:var(--navy)}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]) .logo{color:#fff}}
.logo b{width:36px;height:36px;border-radius:8px;background:var(--navy);color:#fff;display:grid;place-items:center;font:600 20px var(--serif)}
.links{display:flex;gap:4px;margin-left:auto;align-items:center;flex-wrap:wrap}
.links a{font-weight:500;font-size:15px;text-decoration:none;padding:8px 12px;border-radius:6px;color:var(--muted)}
.links a:hover{color:var(--text);background:var(--bg)}
.cart{border:1px solid var(--line);background:var(--card);color:var(--text);font:600 14px var(--sans);padding:8px 14px;border-radius:6px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center}
.cart span{background:var(--blue);color:#fff;border-radius:99px;padding:1px 8px;margin-left:6px;font-size:12px}
.search{flex:1 1 100%;order:5}
.search input{width:100%;border:1px solid var(--line);background:var(--bg);color:var(--text);padding:10px 14px;border-radius:6px;font:400 15px var(--sans)}
@media(min-width:960px){.search{flex:0 1 280px;order:0}}
.mm-btn{font:500 15px var(--sans);padding:8px 12px;border-radius:6px;border:0;background:transparent;color:var(--text);cursor:pointer;display:inline-flex;gap:8px;align-items:center}
.mm-btn::after{content:"";width:7px;height:7px;border-right:2px solid currentColor;border-bottom:2px solid currentColor;transform:rotate(45deg) translateY(-2px);transition:transform .15s}
.mm-btn:hover,.mm-btn[aria-expanded="true"]{background:var(--bg);color:var(--blue)}
.mm-btn[aria-expanded="true"]::after{transform:rotate(225deg)}
.mega{position:absolute;left:0;right:0;top:100%;background:var(--card);border-bottom:1px solid var(--line);box-shadow:0 18px 32px rgba(12,35,64,.14);max-height:calc(100vh - 140px);overflow-y:auto}
.mega[hidden]{display:none}
.mega-in{max-width:1180px;margin:0 auto;padding:28px 24px 32px;display:grid;grid-template-columns:1.5fr auto 1fr;gap:32px}
.mega h3{font:600 13px var(--sans);color:var(--muted);margin:0 0 10px 12px}
.mega-list{display:grid;grid-template-columns:repeat(2,1fr);gap:4px}
.mega-sep{width:1px;background:var(--line)}
.mi{display:block;padding:10px 12px;border-radius:8px;text-decoration:none}
.mi:hover,.mi:focus-visible{background:var(--bg)}
.mi strong{display:block;font-size:15px;color:var(--text)}
.mi span{font-size:13px;color:var(--muted)}
.mega-all{display:inline-block;margin:10px 0 0 12px;font-weight:600;color:var(--blue);text-decoration:none}
.mega-all:hover{text-decoration:underline}
@media(max-width:860px){.mega-in{grid-template-columns:1fr;gap:20px}.mega-sep{width:auto;height:1px}.mega-list{grid-template-columns:1fr}}
.hero{background:var(--hero);color:var(--hero-text);padding:72px 0}
.hero-grid{display:grid;gap:48px;grid-template-columns:1fr;align-items:center}
@media(min-width:960px){.hero-grid{grid-template-columns:1.2fr .8fr}}
.hero h1{font-size:clamp(2rem,5vw,3.4rem)}
.hero p{max-width:56ch;color:#c5d1e0;font-size:1.1rem;margin:20px 0 28px}
.cta{display:flex;gap:12px;flex-wrap:wrap}
.hero .btn{background:#fff;color:var(--navy);border-color:#fff}
.hero .btn:hover{background:#e6edf6}
.hero .btn.ghost{background:transparent;color:#fff;border-color:rgba(255,255,255,.5)}
.panel{background:var(--card);color:var(--text);border-radius:10px;padding:26px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
.panel h3{font-size:1.15rem;margin-bottom:4px}
.panel>p{margin:0 0 16px;color:var(--muted);font-size:14px}
.item{display:flex;justify-content:space-between;gap:12px;align-items:center;padding:14px 0;border-top:1px solid var(--line)}
.item strong{display:block;font-size:15px}
.item span{font-size:13px;color:var(--muted)}
.item b{font-family:var(--serif);color:var(--green);font-size:18px}
.badge{font-size:12px;font-weight:600;color:var(--green);background:rgba(31,138,91,.12);padding:3px 10px;border-radius:99px}
.facts{background:var(--card);border-bottom:1px solid var(--line)}
.facts .wrap{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0}
.fact{padding:26px 24px;border-left:1px solid var(--line)}
.fact:first-child{border-left:0;padding-left:0}
.fact b{display:block;font:600 30px var(--serif);color:var(--blue)}
.fact span{color:var(--muted);font-size:14px}
@media(max-width:700px){.fact{border-left:0;border-top:1px solid var(--line);padding-left:0}.fact:first-child{border-top:0}}
section.s{padding:72px 0}
.head{display:flex;justify-content:space-between;align-items:end;gap:20px;flex-wrap:wrap;margin-bottom:36px}
.head h2{font-size:clamp(1.6rem,3.5vw,2.3rem)}
.head p{margin:10px 0 0;color:var(--muted);max-width:56ch}
.cards{display:grid;gap:24px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
.card{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:26px;box-shadow:var(--shadow);display:flex;flex-direction:column;gap:10px}
.card .ic{width:44px;height:44px;border-radius:8px;background:rgba(26,95,180,.1);color:var(--blue);display:grid;place-items:center;font-weight:700;font-size:20px}
.card h3{font:600 1.05rem var(--sans)}
.card p{margin:0;color:var(--muted);font-size:15px}
.card a{margin-top:auto;padding-top:8px;font-weight:600;color:var(--blue);text-decoration:none}
.card a:hover{text-decoration:underline}
.welfare{background:var(--navy);color:#fff}
.welfare .head p{color:#c5d1e0}
.split{display:grid;gap:24px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr))}
.scheme{background:var(--card);color:var(--text);border-radius:10px;padding:30px;border-top:4px solid var(--gold)}
.scheme:last-child{border-top-color:var(--green)}
.scheme h3{font-size:1.3rem;margin:10px 0 8px}
.scheme>p{margin:0;color:var(--muted)}
.tag{font-size:13px;font-weight:600;color:var(--gold)}
.scheme:last-child .tag{color:var(--green)}
.scheme ul{list-style:none;margin:20px 0 0;padding:0}
.scheme li{padding:12px 0;border-top:1px solid var(--line);font-size:15px}
.empty{background:var(--card);border:1px dashed var(--line);border-radius:10px;padding:44px 24px;text-align:center}
.empty h3{font-size:1.2rem;margin-bottom:6px}
.empty p{margin:0 auto 18px;color:var(--muted);max-width:46ch}
.bundles{display:grid;gap:24px;grid-template-columns:1fr}
@media(min-width:700px){.bundles{grid-template-columns:1fr 1fr}.bundle.lead{grid-column:1/-1}}
@media(min-width:1040px){.bundles{grid-template-columns:1.35fr 1fr 1fr}.bundle.lead{grid-column:auto}}
.bundle{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:28px;box-shadow:var(--shadow);display:flex;flex-direction:column;gap:12px}
.bundle h3{font-size:1.3rem}
.bundle>p{margin:0;color:var(--muted);font-size:15px}
.bundle ul{list-style:none;margin:4px 0 0;padding:0}
.bundle li{padding:9px 0;border-top:1px solid var(--line);font-size:15px;display:flex;justify-content:space-between;gap:12px}
.bundle li span{color:var(--muted)}
.bundle .save{font-size:13px;font-weight:600;color:var(--green)}
.bundle .price{margin-top:auto;padding-top:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.bundle .price b{font:600 26px var(--serif)}
.bundle .price s{color:var(--muted);font-size:15px;margin-left:8px}
.bundle.lead{background:var(--navy);color:#fff;border-color:var(--navy)}
.bundle.lead>p,.bundle.lead li span,.bundle.lead .price s{color:#b8c5d6}
.bundle.lead li{border-top-color:#234263}
.bundle.lead .save{color:#6fd4a4}
.bundle.lead .add{background:#fff;color:var(--navy);border-color:#fff}
.bundle.lead .add:hover,.bundle.lead .add.done{background:#e6edf6;color:var(--navy)}
.bundle .badge{align-self:flex-start}
.bundle.lead .badge{background:rgba(255,255,255,.14);color:#fff}
.grid{display:grid;gap:24px;grid-template-columns:repeat(auto-fill,minmax(230px,1fr))}
.prod{background:var(--card);border:1px solid var(--line);border-radius:10px;overflow:hidden;box-shadow:var(--shadow);display:flex;flex-direction:column}
.shot{height:170px;background:linear-gradient(135deg,#dbe7f6,#eef3fa);display:grid;place-items:center;color:var(--blue);font-weight:600;font-size:14px}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]) .shot{background:linear-gradient(135deg,#1d2b3d,#16202c)}}
.pbody{padding:18px;display:flex;flex-direction:column;gap:4px;flex:1}
.pbody small{color:var(--muted)}
.pbody h3{font:600 1.05rem var(--sans)}
.buy{margin-top:auto;padding-top:14px;display:flex;justify-content:space-between;align-items:center}
.buy b{font:600 20px var(--serif)}
.add{border:1px solid var(--blue);background:transparent;color:var(--blue);border-radius:6px;padding:8px 14px;font:600 14px var(--sans);cursor:pointer}
.add:hover,.add.done{background:var(--blue);color:#fff}
footer{background:var(--navy);color:#b8c5d6;padding:56px 0 28px;font-size:15px;margin-top:auto}
.fgrid{display:grid;gap:32px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}
footer h4{color:#fff;font:600 15px var(--sans);margin:0 0 14px}
footer ul{list-style:none;padding:0;margin:0;display:grid;gap:9px}
footer a{text-decoration:none}footer a:hover{color:#fff;text-decoration:underline}
footer p{margin:0}
footer form{display:flex;gap:8px}
footer input{flex:1;min-width:0;border:1px solid #2f4a6a;background:#10294a;color:#fff;border-radius:6px;padding:10px 12px;font:400 14px var(--sans)}
footer .btn{padding:10px 16px;background:#fff;color:var(--navy);border-color:#fff}
.fine{margin-top:36px;padding-top:20px;border-top:1px solid #234263;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:14px}
</style>
</head>
<body>
<div class="top"><div class="wrap"><span>Free shipping on repurchase bundles over $50</span><span>Same-day dispatch on in-stock items</span></div></div>

<header>
  <div class="nav" style="max-width:1180px;margin:0 auto">
    <a class="logo" href="/"><b>G</b>Giveoraemart</a>
    <div class="search"><input id="q" type="search" placeholder="Search rice, cookware, packages" aria-label="Search the store"></div>
    <nav class="links" aria-label="Main">
      <button class="mm-btn" id="mm-btn" type="button" aria-expanded="false" aria-controls="mega">Services &amp; schemes</button>
      <a href="/shop.php">All items</a>
      <a href="/shop.php?type=package">Bundles</a>
      <?php if ($currentUser): ?>
        <a href="/account.php"><?= htmlspecialchars($currentUser['name']) ?></a>
        <?php if ($currentUser['role'] === 'ADMIN'): ?>
            <a href="/admin/index.php" style="color:var(--blue)">Admin</a>
        <?php endif; ?>
        <a href="/logout.php" style="color:var(--muted)">Logout</a>
      <?php else: ?>
        <a href="/login.php">Sign in</a>
      <?php endif; ?>
      <a class="cart" href="/cart.php" aria-label="Cart">Cart<span id="nav-cart-count"><?= $cartCount ?></span></a>
    </nav>
  </div>
  <div class="mega" id="mega" hidden>
    <div class="mega-in">
      <div>
        <h3>Recharges and bill payments</h3>
        <div class="mega-list">
          <a class="mi" href="/recharge.php?type=mobile"><strong>Mobile recharge</strong><span>Prepaid plans for all operators</span></a>
          <a class="mi" href="/recharge.php?type=dth"><strong>DTH recharge</strong><span>Renew your TV subscription</span></a>
          <a class="mi" href="/recharge.php?type=electricity"><strong>Electricity bill</strong><span>Pay your board bill online</span></a>
          <a class="mi" href="/recharge.php?type=broadband"><strong>Broadband and landline</strong><span>Internet and phone bills</span></a>
          <a class="mi" href="/recharge.php?type=fastag"><strong>FASTag recharge</strong><span>Top up for toll payments</span></a>
          <a class="mi" href="/recharge.php?type=gas"><strong>Gas cylinder booking</strong><span>Book and pay for refills</span></a>
        </div>
        <a class="mega-all" href="/recharge.php">All recharge services</a>
      </div>
      <div class="mega-sep" role="separator" aria-orientation="vertical"></div>
      <div>
        <h3>Welfare schemes</h3>
        <a class="mi" href="/welfare-schemes.php#charity"><strong>Charity Support Scheme</strong><span>₹3,000 × 60 cycles and health cover</span></a>
        <a class="mi" href="/welfare-schemes.php#vidya-vikas"><strong>Vidya Vikas Scheme</strong><span>Student health and accident cover</span></a>
        <a class="mega-all" href="/welfare-schemes.php">All scheme details</a>
      </div>
    </div>
  </div>
</header>

<main>
