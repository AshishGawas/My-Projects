require('dotenv').config();
const path = require('path');
const express = require('express');
const ejsMate = require('ejs-mate');
const morgan = require('morgan');
const methodOverride = require('method-override');
const mongoose = require('mongoose');

const studentRoutes = require('./routes/students');

const app = express();

// Config
const PORT = process.env.PORT || 3000;
const MONGODB_URI = process.env.MONGODB_URI || 'mongodb://127.0.0.1:27017/college_crud';

// DB
mongoose.set('strictQuery', true);
mongoose
  .connect(MONGODB_URI)
  .then(() => console.log('MongoDB connected'))
  .catch((err) => {
    console.error('Mongo connection error:', err);
    process.exit(1);
  });

// Middleware
app.engine('ejs', ejsMate);
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(methodOverride('_method'));
app.use(morgan('dev'));
app.use(express.static(path.join(__dirname, 'public')));

app.get('/', (req, res) => {
  res.redirect('/students');
});

app.use('/students', studentRoutes);

// Error handler (Express 5 handles async rejections)
app.use((err, req, res, next) => {
  console.error(err);
  const isProd = process.env.NODE_ENV === 'production';
  res.status(500).render('error', { title: 'Server Error', error: isProd ? null : err });
});

// 404
app.use((req, res) => {
  res.status(404).render('404', { title: 'Not Found' });
});

app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
