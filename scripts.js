$(document).ready(function () {
    // Navigation functionality
    $(".nav-link").click(function (e) {
      e.preventDefault();
      const target = $(this).attr("href").substring(1);
      $(".content > div").hide();
      $(`#${target}`).show();
    });
  
    // Sample Orders Data
    const orders = [
      { id: 1, customer: "Alice", date: "2024-12-01", status: "Completed", total: "Rp 500,000" },
      { id: 2, customer: "Bob", date: "2024-12-02", status: "Pending", total: "Rp 300,000" },
      { id: 3, customer: "Charlie", date: "2024-12-03", status: "Processing", total: "Rp 700,000" },
    ];
  
    const cakes = [
      { id: 1, image: "https://via.placeholder.com/50", name: "Chocolate Cake", price: "Rp 200,000" },
      { id: 2, image: "https://via.placeholder.com/50", name: "Cheesecake", price: "Rp 250,000" },
    ];
  
    // Load Orders Table
    function loadOrders() {
      $("#ordersTable").empty();
      orders.forEach((order) => {
        $("#ordersTable").append(`
          <tr>
            <td>${order.id}</td>
            <td>${order.customer}</td>
            <td>${order.date}</td>
            <td>${order.status}</td>
            <td>${order.total}</td>
          </tr>
        `);
      });
    }
  
    // Load Cakes Table
    function loadCakes() {
      $("#cakesTable").empty();
      cakes.forEach((cake) => {
        $("#cakesTable").append(`
          <tr>
            <td>${cake.id}</td>
            <td><img src="${cake.image}" alt="${cake.name}" width="50"></td>
            <td>${cake.name}</td>
            <td>${cake.price}</td>
            <td>
              <button class="btn btn-sm btn-warning editCake" data-id="${cake.id}">Edit</button>
              <button class="btn btn-sm btn-danger deleteCake" data-id="${cake.id}">Delete</button>
            </td>
          </tr>
        `);
      });
    }
  
    // Initial Load
    loadOrders();
    loadCakes();
  });
  