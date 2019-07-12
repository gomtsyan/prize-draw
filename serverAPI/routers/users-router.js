const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const keys = require('../config/keys');

// Load input validation
const validateRegisterInput = require('../validation/register');
const validateLoginInput = require('../validation/login');
const validateUpdateManyInput = require('../validation/updateMany');



// Load User model
const User = require('../models/Users');




// @route POST api/users/register
// @desc Register user
// @access Public
router.post('/users/register', function(req, res)  {
    // Form validation
    const errors = validateRegisterInput(req.body).errors;
    const isValid = validateRegisterInput(req.body).isValid;

    // Check validation
    if (!isValid) {
        return res.status(400).json(errors);
    }

    User.findOne({email: req.body.email})
        .then(function(user) {
        if (user) {
            return res.status(400).json({email: 'Email already exists'});
        } else {
            const newUser = new User({
                name: req.body.name,
                email: req.body.email,
                password: req.body.password
            });

            // Hash password before saving in database
            bcrypt.genSalt(10, function(err, salt)  {
                bcrypt.hash(newUser.password, salt, function(err, hash)  {
                    if (err) throw err;
                    newUser.password = hash;
                    newUser
                        .save()
                        .then(function(user){return res.json(user)} )
                        .catch(function(err) { console.log(err)} );
                });
            });
        }
    });
});

// @route POST api/users/login
// @desc Login user and return JWT token
// @access Public
router.post('/users/login', function(req, res)  {
    // Form validation
    const errors = validateLoginInput(req.body).errors;
    const isValid = validateLoginInput(req.body).isValid;

    // Check validation
    if (!isValid) {
        return res.status(400).json(errors);
    }

    const email = req.body.email;
    //const password = req.body.password; TODO: open comment

    // Find user by email
    User.findOne({email:email}).then(function(user)  {
        // Check if user exists
        if (!user) {
            return res.status(404).json({emailnotfound: 'Email not found'});
        }

        // Check password
        /*bcrypt.compare(password, user.password)
            .then(function(isMatch) {
            if (isMatch) {
                // User matched
                // Create JWT Payload
                const payload = {
                    id: user.id,
                    name: user.name
                };

                // Sign token
                jwt.sign(
                    payload,
                    keys.secretOrKey,
                    {
                        expiresIn: 31556926 // 1 year in seconds
                    },
                    function(err, token)  {
                        res.json({
                            success: true,
                            token: 'Bearer ' + token
                        });
                    }
                );
            } else {
                return res
                    .status(400)
                    .json({passwordincorrect: 'Password incorrect'});
            }
        });*/ //TODO: open comment

        // Create JWT Payload
        const payload = {
            id: user.id,
            name: user.name
        };
        // Sign token
        jwt.sign(
            payload,
            keys.secretOrKey,
            {
                expiresIn: 31556926
            },
            function(err, token)  {
                res.json({
                    success: true,
                    token: 'Bearer ' + token
                });
            }
        );
    });
});

router.post('/users/addMoney', function(req, res)  {
    // Form validation
    var errors = validateUpdateManyInput(req.body).errors;
    var isValid = validateUpdateManyInput(req.body).isValid;
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
                    .then(function() {return res.status(200).json({status: 'OK'})} )
                    .catch(function(error) {
                        response.status(500).send(error.message);
                    });

            } else {
                return res.status(400).json({email: 'User not found'});

            }
        });
});

module.exports = router;
