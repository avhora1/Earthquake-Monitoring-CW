function validateForm() {
    const name = document.getElementById('name').value;
    const est_date = document.getElementById('est_date').value;
    const latitude = parseFloat(document.getElementById('latitude').value);
    const longitude = parseFloat(document.getElementById('longitude').value);

    if (name === "" || est_date === "" || isNaN(latitude) || isNaN(longitude)) {
        alert("All fields must be filled out correctly.");
        return false;
    }

    if (latitude < -90 || latitude > 90) {
        alert("Latitude must be between -90 and 90.");
        return false;
    }

    if (longitude < -180 || longitude > 180) {
        alert("Longitude must be between -180 and 180.");
        return false;
    }

    // If validation passes, submit the form
    return true;
}

function validateEarthquakeForm() {
    const country = document.getElementById('country').value;
    const magnitude = parseFloat(document.getElementById('magnitude').value);
    const type = document.getElementById('type').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const latitude = parseFloat(document.getElementById('latitude').value);
    const longitude = parseFloat(document.getElementById('longitude').value);
    const observatory_id = document.getElementById('observatory_id').value;

    if (country === "" || isNaN(magnitude) || type === "" || date === "" || time === "" || isNaN(latitude) || isNaN(longitude) || observatory_id === "") {
        alert("All fields must be filled out correctly.");
        return false;
    }

    if (latitude < -90 || latitude > 90) {
        alert("Latitude must be between -90 and 90.");
        return false;
    }

    if (longitude < -180 || longitude > 180) {
        alert("Longitude must be between -180 and 180.");
        return false;
    }

    // If validation passes, submit the form
    return true;
}

function validateEditEarthquake(form) {
    console.log("script.js loaded"); // shows in browser console F12
    const country = form.country.value.trim();
    const magnitude = parseFloat(form.magnitude.value);
    const type = form.type.value;
    const date = form.date.value;
    const time = form.time.value;
    const latitude = parseFloat(form.latitude.value);
    const longitude = parseFloat(form.longitude.value);
    const observatory_id = form.observatory_id.value;

    if (
      country === "" ||
      isNaN(magnitude) ||
      type === "" ||
      date === "" ||
      time === "" ||
      isNaN(latitude) ||
      isNaN(longitude) ||
      observatory_id === ""
    ) {
      alert("All fields must be filled out correctly.");
      return false;
    }
    if (latitude < -90 || latitude > 90) {
      alert("Latitude must be between -90 and 90.");
      return false;
    }
    if (longitude < -180 || longitude > 180) {
      alert("Longitude must be between -180 and 180.");
      return false;
    }
    return true;
}

function validateArtefactForm() {
    const earthquake_id = document.getElementById('earthquake_id').value;
    const type = document.getElementById('type').value;
    const shelving_loc = document.getElementById('shelving_loc').value;

    if (earthquake_id === "" || type === "" || shelving_loc === "") {
        alert("All fields must be filled out correctly.");
        return false;
    }

    // If validation passes, submit the form
    return true;
}

function validatePalletForm() {
    const pallet_size = document.getElementById('pallet_size').value;

    if (pallet_size === "") {
        alert("Pallet size must be filled out correctly.");
        return false;
    }

    // If validation passes, submit the form
    return true;
}

function validateArtefactForm() {
    const earthquake_id = document.getElementById('earthquake_id').value;
    const type = document.getElementById('type').value;
    const shelving_loc = document.getElementById('shelving_loc').value;

    if (earthquake_id === "" || type === "" || shelving_loc === "") {
        alert("All fields must be filled out correctly.");
        return false;
    }

    // If validation passes, submit the form
    return true
}

function validateAddToShopForm() {
    const artifact_id = document.getElementById('artifact_id').value;
    const price = parseFloat(document.getElementById('price').value);

    if (artifact_id === "" || isNaN(price) || price <= 0) {
        alert("All fields must be filled out correctly.");
        return false;
    }

    // If validation passes, submit the form
    return true;
}

function validateRemoveFromShopForm() {
    const artifact_id = document.getElementById('artifact_id').value;

    if (artifact_id === "") {
        alert("Please select an artefact ID.");
        return false;
    }

    // If validation passes, submit the form
    return true;
}