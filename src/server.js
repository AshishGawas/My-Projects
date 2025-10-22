import express from 'express';
import mongoose from 'mongoose';
import dotenv from 'dotenv';
import cors from 'cors';
import morgan from 'morgan';

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;
const MONGODB_URI = process.env.MONGODB_URI || '';

if (!MONGODB_URI) {
  console.error('Missing MONGODB_URI. Set it in .env');
  process.exit(1);
}

app.use(cors());
app.use(express.json());
app.use(morgan('dev'));
app.use(express.static('public'));

async function start() {
  try {
    await mongoose.connect(MONGODB_URI);
    console.log('Connected to MongoDB');

    app.get('/health', (req, res) => {
      res.json({ ok: true });
    });

    // Routes
    const contactsRouter = (await import('./routes/contacts.js')).default;
    app.use('/api/contacts', contactsRouter);

    app.listen(PORT, () => {
      console.log(`Server listening on http://localhost:${PORT}`);
    });
  } catch (err) {
    console.error('Failed to start server:', err);
    process.exit(1);
  }
}

start();
