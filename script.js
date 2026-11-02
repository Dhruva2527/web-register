$(document).ready(function () {

  // character counter
  $("#message").on("input", function () {
    $("#count").text($(this).val().length);
  });

  function showError(el, msg) {
    var box = el.closest(".field");
    box.find(".error").first().text(msg);
    el.addClass("invalid");
  }

  function clearErrors() {
    $(".error").text("");
    $(".invalid").removeClass("invalid");
  }

  function validate() {
    var ok = true;
    clearErrors();

    var name = $.trim($("#name").val());
    if (!/^[A-Za-z .]{3,}$/.test(name)) { showError($("#name"), "Enter a valid name (letters only, min 3)."); ok = false; }

    var email = $.trim($("#email").val());
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError($("#email"), "Enter a valid email."); ok = false; }

    if (!/^[6-9][0-9]{9}$/.test($.trim($("#phone").val()))) { showError($("#phone"), "Enter a valid 10-digit Indian mobile number."); ok = false; }

    if (!$("#dob").val()) { showError($("#dob"), "Select your date of birth."); ok = false; }

    if (!$("#course").val()) { showError($("#course"), "Select your branch."); ok = false; }

    if (!$("input[name=gender]:checked").length) {
      $("input[name=gender]").first().closest(".field").find(".error").text("Select a gender."); ok = false;
    }

    if (!$("input[name='interests[]']:checked").length) {
      $("input[name='interests[]']").first().closest(".field").find(".error").text("Pick at least one interest."); ok = false;
    }
    return ok;
  }

  $("#regForm").on("submit", function (e) {
    e.preventDefault();
    if (!validate()) return;

    var btn = $("#submitBtn").prop("disabled", true).text("Submitting...");

    $.ajax({
      url: "submit.php",
      type: "POST",
      data: $(this).serialize(),
      success: function (html) {
        $("#result").html(html).removeClass("hidden").hide().fadeIn(400);
        $("html, body").animate({ scrollTop: $("#result").offset().top - 20 }, 500);
      },
      error: function () {
        $("#result").html('<div class="alert">Server error. Please try again.</div>').removeClass("hidden");
      },
      complete: function () {
        btn.prop("disabled", false).text("Register");
      }
    });
  });

  // buttons inside the PHP-generated result card
  $("#result").on("click", "#printBtn", function () { window.print(); });
  $("#result").on("click", "#resetBtn", function () {
    $("#regForm")[0].reset();
    $("#count").text(0);
    $("#result").fadeOut(200, function () { $(this).addClass("hidden").empty(); });
    $("html, body").animate({ scrollTop: 0 }, 400);
  });
});
