module.exports = {
    mongoURI: 'mongodb://127.0.0.1:27017/bank_api',
    secretOrKey: 'secret',
    key: 'sid',
    cookie: {
        path: '/',
        httpOnly: true,
        maxAge: null
    }
};
