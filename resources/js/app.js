import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Start Alpine safely (fix dropdown after redirect/new invoice)
document.addEventListener('DOMContentLoaded', () => {
    if (!window.__alpine_started) {
        Alpine.start();
        window.__alpine_started = true;
    }
});

// Fix Alpine after Laravel redirect / bfcache
window.addEventListener('pageshow', function () {
    if (!window.__alpine_started) {
        Alpine.start();
        window.__alpine_started = true;
    }
});




// =============================
// RATING FEATURE (WITH STORAGE)
// =============================

document.addEventListener("DOMContentLoaded", function () {

    const starContainer = document.getElementById("starContainer");
    const ratingText = document.getElementById("ratingText");
    const thankYouMessage = document.getElementById("thankYouMessage");

    if (starContainer) {

        let totalRatings = localStorage.getItem("totalRatings")
            ? parseInt(localStorage.getItem("totalRatings"))
            : 1000;

        let totalScore = localStorage.getItem("totalScore")
            ? parseFloat(localStorage.getItem("totalScore"))
            : 1000 * 4;

        function renderStars(selectedRating = 4) {

            starContainer.innerHTML = "";

            for (let i = 1; i <= 5; i++) {

                const star = document.createElement("span");
                star.innerHTML = "★";
                star.className = "text-4xl cursor-pointer transition";

                if (i <= selectedRating) {
                    star.classList.add("text-yellow-400");
                } else {
                    star.classList.add("text-gray-300");
                }

                star.addEventListener("click", function () {
                    handleRating(i);
                });

                starContainer.appendChild(star);
            }
        }

        function updateDisplay(selectedRating = 4) {
            const averageRating = (totalScore / totalRatings).toFixed(1);

            ratingText.innerText =
                averageRating + " / 5 (" +
                totalRatings.toLocaleString() +
                " ratings)";

            renderStars(selectedRating);
        }

        function handleRating(userRating) {

            totalRatings++;
            totalScore += userRating;

            localStorage.setItem("totalRatings", totalRatings);
            localStorage.setItem("totalScore", totalScore);

            updateDisplay(userRating);

            if (thankYouMessage) {
                thankYouMessage.classList.remove("hidden");
            }
        }

        updateDisplay(4);
    }

 // =============================
// HERO REGISTER FORM LOGIC
// =============================

var passInput = document.getElementById('password');
var helper = document.getElementById('passwordHelper');
var toggleBtn = document.getElementById('hero-password-toggle');
var eye = document.getElementById('hero-password-eye');
var eyeOff = document.getElementById('hero-password-eye-off');

if (passInput) {

    passInput.addEventListener('focus', function () {
        if (helper) helper.classList.remove('hidden');
        var strengthWrap = document.getElementById('passwordStrength');
        if (strengthWrap) strengthWrap.classList.remove('hidden');
    });

    passInput.addEventListener('blur', function () {
        if (helper) helper.classList.add('hidden');
        var strengthWrap = document.getElementById('passwordStrength');
        if (strengthWrap && !passInput.value)
            strengthWrap.classList.add('hidden');
    });

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            toggleBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            if (eye) eye.classList.toggle('hidden', isPassword);
            if (eyeOff) eyeOff.classList.toggle('hidden', !isPassword);
        });
    }

    passInput.addEventListener('input', function () {

        var strengthWrap = document.getElementById('passwordStrength');
        var bar = document.getElementById('passwordBar');
        var text = document.getElementById('passwordText');

        if (!strengthWrap || !bar || !text) return;

        var val = passInput.value;
        var score = 0;

        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        var styles = [
            ['w-1/4 bg-red-500', 'Weak password'],
            ['w-2/4 bg-yellow-500', 'Medium password'],
            ['w-3/4 bg-blue-500', 'Good password'],
            ['w-full bg-green-600', 'Strong password']
        ];

        if (score === 0) {
            bar.className = '';
            text.textContent = '';
            return;
        }

        bar.className = 'h-2 rounded transition-all duration-300 ' + styles[score-1][0];
        text.textContent = styles[score-1][1];
    });
}

});
