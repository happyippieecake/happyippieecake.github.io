$(document).ready(function () {
    // Navigation functionality
    $(".nav-link").click(function (e) {
      e.preventDefault();
      const target = $(this).attr("href").substring(1);
      $(".content > div").hide();
      $(`#${target}`).show();
    });
  
    // Sample Data
    const cakes = [
      { id: 1, image: "https://via.placeholder.com/50", name: "Chocolate Cake", price: "Rp 200,000" },
      { id: 2, image: "https://via.placeholder.com/50", name: "Cheesecake", price: "Rp 250,000" },
    ];
  
    // Load Cakes Table
    function loadCakes() {
      $("#cakesTable").empty();
      cakes.forEach((cake, index) => {
        $("#cakesTable").append(`
          <tr>
            <td>${index + 1}</td>
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
    loadCakes();
  
    // Edit Cake Modal
    let editIndex = null;
    $(document).on("click", ".editCake", function () {
      const id = $(this).data("id");
      const cake = cakes.find((c) => c.id === id);
      editIndex = cakes.findIndex((c) => c.id === id);
  
      // Populate modal with current data
      $("#editCakeName").val(cake.name);
      $("#editCakePrice").val(cake.price.replace(/[^\d]/g, ""));
      $("#editCakeImage").val(cake.image);
  
      // Show modal
      $("#editCakeModal").modal("show");
    });
  
    // Save Edited Cake
    $("#editCakeForm").submit(function (e) {
      e.preventDefault();
  
      // Update cake data
      cakes[editIndex].name = $("#editCakeName").val();
      cakes[editIndex].price = `Rp ${parseInt($("#editCakePrice").val()).toLocaleString()}`;
      cakes[editIndex].image = $("#editCakeImage").val();
  
      // Reload table
      loadCakes();
  
      // Hide modal
      $("#editCakeModal").modal("hide");
    });
  });
  