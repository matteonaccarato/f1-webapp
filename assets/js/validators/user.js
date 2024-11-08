const MAX_LENGTH = 255;

/**
 * Error detected in input fields,
 * so showing a proper message (err_msg) and disabling submit button is performed.
 * @param id
 * @param err_msg
 */
const err_input_info = (id, err_msg) => {
    $(`#input-info-${id} span:first-child`).text("warning").addClass("text-danger");
    $(`#input-info-${id} span:nth-child(2)`).html(`${err_msg}`).addClass("text-danger");
    $(`#input-info-${id}`).removeClass("d-none");
    $(".btn-submit,#btn-submit").attr("disabled", true)
}

const clear_input_info = ids => {
    // foreach used to clear input communication with ids: [pass, pass-confirm]
    ids.forEach(id => {
        $(`#input-info-${id} span:first-child`).text("").removeClass("text-danger");
        $(`#input-info-${id} span:nth-child(2)`).text("").removeClass("text-danger");
        $(`#input-info-${id}`).addClass("d-none");
    })

}

const validateMaxFiles = (id, value, params) => {
    const image_local = $("#images-local")[0];
    return (image_local)? image_local.files.length <= params:true;
}

const validateMaxLength = (id, value) => {
    // if value is undefined, it means it is not shown on the page.
    // it does not count as valid/not valid.
    return (value === undefined)? true : value.length <= MAX_LENGTH;
}

const validateEmail = (id, email) => {
    if (email === '') return true;
    return validateMaxLength(id, email) && String(email)
        .toLowerCase()
        .match(
            /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/
        );
};

const validatePassword = (id, password) => {
    // if password is undefined, it means it is not shown on the page.
    // it does not count as valid/not valid.
    if (password === undefined) return true;

    const isValid = password.length <= MAX_LENGTH;

    if (id === "pass") {
        const confirm = $("#pass-confirm").val();
        if (confirm !== undefined) {
            //console.log("C " + confirm)
            return isValid && password === confirm;
        }
        return isValid;
    } else {
        const original_password = $("#pass").val();
        if (original_password !== undefined) {
            return isValid && original_password === password;
        }
        return isValid;
    }
}

/**
 * Object designed to store validating information
 * key: string
 * ids: array containing the list of ids to perform the validator function on
 * validator: function used to validate input given
 * params: containing validator function parameters
 * err_msg: array of strings where to specify message if an error is detected
 */
const validators_user = {
    "firstname": {
        ids: ["firstname"],
        validator: validateMaxLength,
        err_msg: ["First name is too <strong class='text-danger'>LONG</strong>"]
    },
    "lastname": {
        ids: ["lastname"],
        validator: validateMaxLength,
        err_msg: ["Last name is too <strong class='text-danger'>LONG</strong>"]
    },
    "email": {
        ids: ["email"],
        validator: validateEmail,
        err_msg: ["E-mail <strong class='text-danger'>NOT</strong> valid"]
    },
    "password": {
        ids: ["pass", "pass-confirm"],
        validator: validatePassword,
        err_msg: ["Password is <strong class='text-danger'>NOT</strong> correct",
            "Retyped password does <strong class='text-danger'>NOT</strong> match with the original one"]
    },
    "img_url": {
        ids: ["img_url_1"],
        validator: validateMaxLength,
        err_msg: ["Image url is too <strong class='text-danger'>LONG</strong>"]
    },
    "image_local": {
        ids: ["image-local"],
        validator: validateMaxFiles,
        params: 1,
        err_msg: ["You can upload a maximum of <strong class='text-danger'>ONE</strong> image"],
        err_id: "image-local"
    }
}

/**
 * Enable (or Disable) btn-submit if essential inputs are valid (or NOT)
 * @param validators
 */
const check_all = validators => {
    let isValid = true;
    for (const [key, value] of Object.entries(validators)) {
        value.ids.forEach((id, index) => {
            const input_element = $(`#${id}`);
            if (input_element !== undefined && input_element.val() !== undefined) {
                isValid = isValid && value.validator(id, input_element.val());
            }
        })
        if (!isValid) break;
    }
    if (isValid) {
        $(".btn-submit,#btn-submit").attr("disabled", false)
    }
}

/**
 * Add "input" event listener on each input field
 */
for (const value of Object.values(validators_user)) {
    // console.log(key, value);
    value.ids.forEach((id, index) => {
        const dom_element = $(`#${id}`)
        if (dom_element.val() !== undefined) {
            dom_element.on('input', event => {
                if (!value.validator(id, event.target.value)) {
                    err_input_info(id, value.err_msg[index])
                } else {
                    clear_input_info(value.ids)
                    check_all(validators_user)
                }
            })
        }
    })
}

/**
 * Perform check_email function on each element of the array.
 * The function asks a backend controller function if the email given is already in the database
 * (email has to be unique)
 */
const element = [document.getElementById('register-form'), document.getElementById("profile-data")];
element.forEach(check_email);
function check_email() {
    addEventListener("change", function (event){
        event.preventDefault();

        const original_email = document.getElementById("original-email");
        const form_email = document.getElementById("email");
        const status_email = document.getElementById("status");
        const status_symbol = document.getElementById("status_symbol");
        const submit = document.getElementsByClassName("btn-submit")
        const email = form_email?form_email.value.trim():"";
        // 1st condition => it means that the loaded page is the registration one (there is no "original-email" input), so the email check is required
        // Otherwise, checking if the new email provided is the same as the previous one is needed
        if (!original_email || original_email.value.trim() !== email) {
            fetch(`http://localhost:63342/f1-webapp/controllers/auth/check_email_client.php?email=${encodeURIComponent(email)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        status_email.textContent = 'Email already in use, try a different one.';
                        status_symbol.style.removeProperty('display');
                        for (let i = 0; i < submit.length; i++) {
                            submit[i].disabled = true;
                        }
                    }
                    if (data.exists_no_match) {
                        status_email.textContent = '';
                        status_symbol.style.display = 'none';
                        for (let i = 0; i < submit.length; i++) {
                            submit[i].disabled = false;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error in API call:', error);
                    status_email.textContent = 'Email check failed.';
                });
        }
    })
}