const Validator = require('validator');
const isEmpty = require('is-empty');

module.exports = function(data) {
    var errors = {};

    // Convert empty fields to an empty string so we can use validator functions
    data.email = !isEmpty(data.email) ? data.email : '';

    if (!parseInt(data.money)) {
        errors.money = 'Money is required';
    }

    // Email checks
    if (Validator.isEmpty(data.email)) {
        errors.email = 'Email field is required';
    } else if (!Validator.isEmail(data.email)) {
        errors.email = 'Email is invalid';
    }

    return {
        errors:errors,
        isValid: isEmpty(errors)
    };
};
