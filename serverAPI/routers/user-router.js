const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const keys = require('../config/keys');

// Load input validation

const validateUpdateInput = require('../validation/update');
const middleware = require('../middleware/authMiddleware');

// Load User model
const User = require('../models/Users');

// @route POST api/user/update
// @desc Update user money
// @access Public
router.put('/user', middleware.checkToken,  function(req, res)  {
    // Form validation
    const errors = validateUpdateInput(req.body).errors;
    const isValid = validateUpdateInput(req.body).isValid;

    // Check validation
    if (!isValid) {
        return res.status(400).json(errors);
    }

    const email = req.body.email;
    var money = parseInt(req.body.money);


    User.findOne({email: req.body.email})
        .then(function(user) {
            if (user) {
                if(user.money) {
                    money += parseInt(user.money);
                }

                User
                    .update(
                        {email: email},
                        {
                            $set: {
                                money: money
                            }
                        }
                    )
                    .then(function() {return res.status(200).send('ok')} )
                    .catch(function(error) {
                        console.log(error.message);
                        response.status(500).send(error.message);
                    });

                //return res.status(200).json({status: 'ok'});
            } else {
                return res.status(400).json({email: 'User not found'});

            }
        });
});

module.exports = router;
