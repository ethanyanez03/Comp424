const express = require('express');
const bodyParser = require('body-parser');
const axios = require('axios');

const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Your reCAPTCHA secret key
const RECAPTCHA_SECRET_KEY = '6Ld8Rh8rAAAAAF97SE6lfTciHYC9vuiv8Hp1RAKL'; // Replace with your secret key

// POST endpoint for handling signup
app.post('/signup', async (req, res) => {
  const { recaptchaResponse, firstName, lastName, email, username, password, securityQuestion, securityAnswer } = req.body;

  // Verify the reCAPTCHA response with Google
  try {
    const response = await axios.post('https://www.google.com/recaptcha/api/siteverify', null, {
      params: {
        secret: RECAPTCHA_SECRET_KEY,
        response: recaptchaResponse,
      },
    });

    const result = response.data;

    if (result.success) {
      // Proceed with your signup logic (store data in the database, etc.)
      res.json({ message: 'Signup successful!' });
    } else {
      res.status(400).json({ error: 'reCAPTCHA verification failed. Please try again.' });
    }
  } catch (error) {
    console.error('Error verifying reCAPTCHA:', error);
    res.status(500).json({ error: 'Internal server error' });
  }
});

// Start the server
app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
});
