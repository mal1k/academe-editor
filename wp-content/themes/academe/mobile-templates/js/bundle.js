(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

jQuery(window).on("load", function () {
  (function () {
    var passwordInput = document.getElementById("txtPassword"),
        toggle = document.getElementById("btnToggle"),
        icon = document.getElementById("eyeIcon");

    function togglePassword() {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.add("fa-eye-slash");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
      }
    }

    if (passwordInput) {
      toggle.addEventListener("click", togglePassword, false);
    }
  })();

  (function () {
    jQuery(document).on("change input", "#login-form", function (e) {
      var name = jQuery("#txtUserName").val();
      var password = jQuery("#txtPassword").val();

      if (name != "" && password != "") {
        jQuery("#login-form-submit").removeAttr('disabled');
      } else {
        jQuery("#login-form-submit").attr('disabled', true);
        return false;
      }
    });
    jQuery(document).on("click", "#login-form-submit", function (e) {
      var name = jQuery("#txtUserName").val();
      var password = jQuery("#txtPassword").val();

      if (name == "") {
        jQuery(".error-text").html("You have to fill out all input boxes");
        return false;
      } else if (password.length < 3) {
        jQuery(".error-text").html("Password have to contain at least 3 characters");
        return false;
      } else {
        jQuery('.error-text').html('');
      }
    });
  })();

  (function () {
    jQuery(document).on("change input", "#quiz", function (e) {
      var steps = jQuery("#quiz fieldset");
      steps.each(function () {
        var step = jQuery(this);
        disabledNext(step);
      });

      function disabledNext(step) {
        if (step.find("input[type='radio']").length) {
          if (step.find("input[type='radio']:checked").length) {
            step.find('.btn.next').removeAttr('disabled');
            step.find('.btn.end').removeAttr('disabled');
          }
        } else if (step.find("textarea").length) {
          if (step.find("textarea").val().length != "") {
            step.find('.btn.next').removeAttr('disabled');
            step.find('.btn.end').removeAttr('disabled');
          } else {
            step.find('.btn.next').attr('disabled', true);
            step.find('.btn.end').attr('disabled', true);
          }
        } else if (step.find("input[type='checkbox']").length) {
          if (step.find("input[type='checkbox']:checked").length) {
            step.find('.btn.next').removeAttr('disabled');
            step.find('.btn.end').removeAttr('disabled');
          } else {
            step.find('.next').attr('disabled', true);
            step.find('.btn.end').attr('disabled', true);
          }
        }
      }
    });
    jQuery(document).on("click", "#quiz .next", function (e) {
      e.preventDefault();
      var step = jQuery(this).closest('fieldset');
      step.removeClass('active');
      jQuery('.wait-screen').show();
      step.next().addClass('active');
      jQuery(window).scrollTop(0);
      setTimeout(function () {
        jQuery('.wait-screen').hide();
      }, 3000);
    });
  })();
});

},{}]},{},[1])

//# sourceMappingURL=bundle.js.map
