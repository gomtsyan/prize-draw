const mongoose = require('mongoose');
const Schema = mongoose.Schema;

// Create Schema
const UsersMoneySchema = new Schema({
    email: {
        type: String,
        required: true
    },
    money: {
        type: Number,
        required: false
    },
    date: {
        type: Date,
        default: Date.now
    }
});

const usersMoney = mongoose.model('usersMoney', UsersMoneySchema);

module.exports = usersMoney;
