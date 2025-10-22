const express = require('express');
const Student = require('../models/Student');

const router = express.Router();

// List all students
router.get('/', async (req, res) => {
  const { q } = req.query;
  const query = q
    ? { $or: [
        { name: { $regex: q, $options: 'i' } },
        { email: { $regex: q, $options: 'i' } },
        { course: { $regex: q, $options: 'i' } },
      ] }
    : {};
  const students = await Student.find(query).sort({ createdAt: -1 });
  res.render('students/index', { title: 'Students', students, q });
});

// New student form
router.get('/new', (req, res) => {
  res.render('students/new', { title: 'Add Student' });
});

// Create student
router.post('/', async (req, res) => {
  try {
    await Student.create(req.body);
    res.redirect('/students');
  } catch (err) {
    res.status(400).render('students/new', { title: 'Add Student', error: err.message });
  }
});

// Show student
router.get('/:id', async (req, res) => {
  const student = await Student.findById(req.params.id);
  if (!student) return res.status(404).render('404', { title: 'Not Found' });
  res.render('students/show', { title: student.name, student });
});

// Edit form
router.get('/:id/edit', async (req, res) => {
  const student = await Student.findById(req.params.id);
  if (!student) return res.status(404).render('404', { title: 'Not Found' });
  res.render('students/edit', { title: `Edit ${student.name}`, student });
});

// Update student
router.put('/:id', async (req, res) => {
  try {
    const student = await Student.findByIdAndUpdate(req.params.id, req.body, { runValidators: true, new: true });
    if (!student) return res.status(404).render('404', { title: 'Not Found' });
    res.redirect(`/students/${student._id}`);
  } catch (err) {
    const student = await Student.findById(req.params.id);
    res.status(400).render('students/edit', { title: 'Edit Student', student, error: err.message });
  }
});

// Delete student
router.delete('/:id', async (req, res) => {
  await Student.findByIdAndDelete(req.params.id);
  res.redirect('/students');
});

module.exports = router;
