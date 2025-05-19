// Card type patterns for detection
const cardTypes = [
    { name: "Visa", pattern: /^4[0-9]{12}(?:[0-9]{3})?$/, logo: "https://img.icons8.com/color/48/000000/visa.png", cvv: 3 },
    { name: "MasterCard", pattern: /^5[1-5][0-9]{14}$/, logo: "https://img.icons8.com/color/48/000000/mastercard-logo.png", cvv: 3 },
    { name: "Amex", pattern: /^3[47][0-9]{13}$/, logo: "https://img.icons8.com/color/48/000000/amex.png", cvv: 4 }
];

// Helper: Get card type by number
function detectCardType(number) {
    number = number.replace(/\D/g, "");
    return cardTypes.find(type => type.pattern.test(number));
}
// Luhn check for card number
function isValidCardNumber(number) {
    number = number.replace(/\D/g, "");
    let sum = 0, shouldDouble = false;
    for (let i = number.length - 1; i >= 0; i--) {
        let digit = parseInt(number[i]);
        if (shouldDouble) {
            if ((digit *= 2) > 9) digit -= 9;
        }
        sum += digit;
        shouldDouble = !shouldDouble;
    }
    return (sum % 10) === 0;
}

// UK Phone: Accepts formats like 07123 456789 or 07123456789 etc
function isValidUKPhone(phone) {
    return /^(\+44\s?7\d{3}|\(?07\d{3}\)?)\s?\d{3}\s?\d{3}$/.test(phone.replace(/\s+/g, ''));
}

// Expiry: Expects MM/YY or MM/YYYY
function isValidExpiry(date) {
    if (!/^(0[1-9]|1[0-2])\/?([0-9]{2}|[0-9]{4})$/.test(date)) return false;
    let [mm, yy] = date.split('/');
    if (yy.length === 2) yy = "20" + yy;
    const exp = new Date(+yy, mm); // next month, so always end of exp month
    const now = new Date();
    return exp > now;
}

function showCardLogo(number) {
    const logoSpan = document.getElementById('card-logo');
    let detected = detectCardType(number);
    // Show only if enough digits
    logoSpan.innerHTML = detected ? `<img src="${detected.logo}" title="${detected.name}" style="height:24px;">` : "";
    return detected;
}

// -- Main validation
document.querySelector('.needs-validation').addEventListener('submit', function (event) {
    let form = event.target;
    let valid = true;

    // First and Last name, max 30 chars (or your choice)
    let firstName = form.querySelector('#firstName').value.trim();
    let lastName = form.querySelector('#lastName').value.trim();
    if (firstName.length < 1 || firstName.length > 30) {
        valid = false;
        form.querySelector('#firstName').classList.add('is-invalid');
        form.querySelector('#firstName').classList.remove('is-valid');
    } else {
        form.querySelector('#firstName').classList.remove('is-invalid');
        form.querySelector('#firstName').classList.add('is-valid');
    }
    if (lastName.length < 1 || lastName.length > 30) {
        valid = false;
        form.querySelector('#lastName').classList.add('is-invalid');
        form.querySelector('#lastName').classList.remove('is-valid');
    } else {
        form.querySelector('#lastName').classList.remove('is-invalid');
        form.querySelector('#lastName').classList.add('is-valid');
    }

    // Email
    let email = form.querySelector('#email').value.trim();
    // basic username@domain.tld check:
    if (!/^[^@]+@[^@]+\.[a-z]{2,}$/i.test(email)) {
        valid = false;
        form.querySelector('#email').classList.add('is-invalid');
        form.querySelector('#email').classList.remove('is-valid');
    } else {
        form.querySelector('#email').classList.remove('is-invalid');
        form.querySelector('#email').classList.add('is-valid');
    }

    // UK phone number
    let phone = form.querySelector('#phoneNumber').value.trim();
    if (!isValidUKPhone(phone)) {
        valid = false;
        form.querySelector('#phoneNumber').classList.add('is-invalid');
        form.querySelector('#phoneNumber').classList.remove('is-valid');
    } else {
        form.querySelector('#phoneNumber').classList.remove('is-invalid');
        form.querySelector('#phoneNumber').classList.add('is-valid');
    }

    // Card number detection
    let ccNum = form.querySelector('#cc-number').value.replace(/\s+/g, '');
    let cardInfo = detectCardType(ccNum);
    if (!isValidCardNumber(ccNum) || !cardInfo) {
        valid = false;
        form.querySelector('#cc-number').classList.add('is-invalid');
        form.querySelector('#cc-number').classList.remove('is-valid');
    } else {
        form.querySelector('#cc-number').classList.remove('is-invalid');
        form.querySelector('#cc-number').classList.add('is-valid');
    }
    // Card logo always updates as you type
    showCardLogo(ccNum);

    // Expiry date
    let expiry = form.querySelector('#cc-expiration').value.trim();
    if (!isValidExpiry(expiry)) {
        valid = false;
        form.querySelector('#cc-expiration').classList.add('is-invalid');
        form.querySelector('#cc-expiration').classList.remove('is-valid');
    } else {
        form.querySelector('#cc-expiration').classList.remove('is-invalid');
        form.querySelector('#cc-expiration').classList.add('is-valid');
    }

    // CVV: 3 or 4 digits depending on card type
    let cvvEl = form.querySelector('#cc-cvv');
    let cvv = cvvEl.value.trim();
    let cvvValid = false;
    if (cardInfo) {
        if (cardInfo.cvv === 4 && /^\d{4}$/.test(cvv)) cvvValid = true;
        if (cardInfo.cvv === 3 && /^\d{3}$/.test(cvv)) cvvValid = true;
    }
    if (!cvvValid) {
        valid = false;
        cvvEl.classList.add('is-invalid');
        cvvEl.classList.remove('is-valid');
    } else {
        cvvEl.classList.remove('is-invalid');
        cvvEl.classList.add('is-valid');
    }

    if (!valid) {
        event.preventDefault();
        event.stopPropagation();
    }
}, false);

// Show card logo on typing card number
document.getElementById('cc-number').addEventListener('input', function (e) {
    let card = showCardLogo(e.target.value);
    // Optionally set maxlength for CVV
    let cvvEl = document.getElementById('cc-cvv');
    if (card) {
        cvvEl.setAttribute('maxlength', card.cvv);
    } else {
        cvvEl.removeAttribute('maxlength');
    }
});