#!/bin/bash

echo "🎓 College Student Management System - Installation Script"
echo "=========================================================="

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js first."
    echo "   Download from: https://nodejs.org/"
    exit 1
fi

echo "✅ Node.js found: $(node --version)"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm first."
    exit 1
fi

echo "✅ npm found: $(npm --version)"

# Install dependencies
echo "📦 Installing dependencies..."
npm install

if [ $? -eq 0 ]; then
    echo "✅ Dependencies installed successfully!"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi

# Check if MongoDB is running (optional)
echo "🔍 Checking MongoDB connection..."
if command -v mongo &> /dev/null; then
    echo "✅ MongoDB CLI found"
else
    echo "⚠️  MongoDB CLI not found. Make sure MongoDB is installed and running."
    echo "   Local: https://www.mongodb.com/try/download/community"
    echo "   Cloud: https://www.mongodb.com/atlas"
    echo "   Docker: docker run -d -p 27017:27017 --name mongodb mongo"
fi

echo ""
echo "🚀 Installation complete!"
echo ""
echo "To start the application:"
echo "  npm run dev    (development mode with auto-restart)"
echo "  npm start      (production mode)"
echo ""
echo "Then open: http://localhost:3000"
echo ""
echo "📚 Read README.md for detailed instructions"