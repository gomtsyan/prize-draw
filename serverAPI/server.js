const express = require('express');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const cookieParser = require('cookie-parser');
const session = require('express-session');
const cors = require('cors');
const passport = require('passport');
const mongoose = require('mongoose');
const keys = require('./config/keys');
const usersRouter = require('./routers/users-router');
const userRouter = require('./routers/user-router');
const middleware = require('./middleware/authMiddleware');

// initialization
const PORT = process.env.PORT || 8000;
const uri = keys.mongoURI;
// configure server
const app = express();

// Connect to MongoDB
mongoose
    .connect(
        uri,
        { useNewUrlParser: true }
    )
    .then(function() {
        console.log('MongoDB successfully connected');
    })
    .catch(function(err) {
        console.log(err);
    });

app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(cors());
app.use(morgan('combined'));

// Passport middleware
app.use(passport.initialize());

// Passport config
require('./config/passport')(passport);

app.use('/api', usersRouter);
app.use('/api', userRouter);

// start server
app.listen(PORT, function() {
    console.log('Server up and running on port ' + PORT + ' !');
} );
