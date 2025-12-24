<?php
// views/frontend/product/index.php
$baseUrl = $baseUrl ?? '/eventprint/public';
?>

<style>
  /* Products Page Specific Styles */
  body {
    background-color: #F8FAFC;
  }

  .products-section {
    padding: 40px 0 80px;
  }

  .products-layout {
    display: flex;
    gap: 32px;
    align-items: flex-start;
  }

  /* --- SIDEBAR CONTAINER --- */
  .products-sidebar {
    width: 280px;
    flex-shrink: 0;
    background: #FFFFFF;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
    position: sticky;
    top: 100px;
    /* Scrollable wrapper per requirement */
    height: fit-content;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
    /* Allow flow */
    display: block;
  }

  .sidebar-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 24px;
    color: var(--gray-900);
    padding-left: 4px;
  }

  /* --- SIDEBAR GROUP --- */
  .category-list {
    list-style: none;
    /* Reset */
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0;
    /* Handled by margins */
  }

  .sidebar-group {
    display: block;
    margin-bottom: 8px;
    width: 100%;
  }

  /* --- HEADER BUTTON --- */
  .sidebar-head {
    /* Reset button styles */
    appearance: none;
    background: transparent;
    border: none;

    /* Layout */
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 10px 12px;
    border-radius: 10px;

    /* Font */
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--gray-600);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
  }

  .sidebar-head:hover {
    color: var(--primary-cyan);
    background: #F9FAFB;
  }

  .sidebar-head.active {
    color: var(--gray-900);
    font-weight: 700;
  }

  /* Icon Rotation */
  .category-icon {
    width: 20px;
    height: 20px;
    color: #9CA3AF;
    transition: transform 0.2s ease;
    flex-shrink: 0;
  }

  .sidebar-head[aria-expanded="true"] .category-icon {
    transform: rotate(180deg);
    color: var(--gray-900);
  }

  /* --- SUBMENU BODY (Flow Layout) --- */
  .sidebar-body {
    /* Flex Column Layout */
    display: flex;
    flex-direction: column;
    gap: 4px;
    /* Gap between items */
    padding: 6px 0 6px 0;
    /* Padding vertical */
    margin-left: 0;
    /* Reset */
    width: 100%;
    position: static;
    /* NO ABSOLUTE */
  }

  /* Hidden state handled by [hidden] attribute */
  .sidebar-body[hidden] {
    display: none;
  }

  /* --- SUBMENU ITEM (Button) --- */
  .sidebar-item {
    /* Reset button styles */
    appearance: none;
    background: transparent;
    border: none;

    /* Layout */
    display: flex;
    align-items: center;
    width: 100%;
    height: 38px;
    /* Fixed height per request */
    padding: 0 12px 0 32px;
    /* Indent */

    /* Font */
    font-family: var(--font-body);
    font-size: 0.95rem;
    color: var(--gray-500);
    text-align: left;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;
    text-decoration: none;
  }

  .sidebar-item:hover {
    color: var(--primary-cyan);
    background: #F0F9FF;
  }

  .sidebar-item.active {
    background: #F3F4F6;
    color: var(--gray-900);
    font-weight: 600;
  }

  /* products content */
  .products-content {
    flex: 1;
  }

  .products-header {
    margin-bottom: 24px;
  }

  .breadcrumbs {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
  }

  .current-category-name {
    color: var(--primary-cyan);
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .products-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .products-sidebar {
      width: 240px;
    }
  }

  @media (max-width: 768px) {
    .products-layout {
      flex-direction: column;
    }

    .products-sidebar {
      width: 100%;
      position: static;
      margin-bottom: 24px;
      max-height: none;
    }
  }

  /* Small mobile optimization now handled in main.css */

  /* Animation */
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-5px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

<!-- Main Content -->
<div class="container">
  <div class="products-layout">

    <!-- Sidebar -->
    <aside class="products-sidebar">
      <h2 class="sidebar-title">Kategori</h2>
      <ul id="categorySidebar" class="category-list">
        <!-- Data Driven Sidebar -->
        <li class="skeleton-text" style="height: 40px; margin-bottom: 10px;"></li>
        <li class="skeleton-text" style="height: 40px; margin-bottom: 10px;"></li>
      </ul>
    </aside>

    <!-- Product Grid -->
    <main class="products-content">
      <div class="products-header">
        <div id="pageTitle" class="breadcrumbs">
          Product
        </div>
      </div>

      <div id="productGrid" class="products-grid">
        <!-- Data Driven Products -->
      </div>
    </main>
  </div>
</div>