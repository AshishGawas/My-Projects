import mongoose from 'mongoose';

const ContactSchema = new mongoose.Schema(
  {
    name: { type: String, required: true, trim: true },
    email: { type: String, required: true, trim: true, lowercase: true },
    phone: { type: String, required: true, trim: true },
    address: { type: String, trim: true },
  },
  { timestamps: true }
);

ContactSchema.index({ email: 1 }, { unique: true });

export const Contact = mongoose.model('Contact', ContactSchema);
