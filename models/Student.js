const mongoose = require('mongoose');

const studentSchema = new mongoose.Schema(
  {
    name: { type: String, required: true, trim: true },
    email: { type: String, required: true, unique: true, lowercase: true, trim: true },
    course: { type: String, required: true, trim: true },
    year: { type: Number, min: 1, max: 8, default: 1 },
    gpa: { type: Number, min: 0, max: 10, default: 0 },
  },
  { timestamps: true }
);

module.exports = mongoose.model('Student', studentSchema);
