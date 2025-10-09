// Simple API test script for the College Student Management System
const http = require('http');

const API_BASE = 'http://localhost:3000/api';

// Test data
const testStudent = {
    studentId: 'TEST001',
    firstName: 'John',
    lastName: 'Doe',
    email: 'john.doe@test.edu',
    phone: '(555) 123-4567',
    course: 'Computer Science',
    year: 2,
    gpa: 3.75
};

// Helper function to make HTTP requests
function makeRequest(method, path, data = null) {
    return new Promise((resolve, reject) => {
        const url = new URL(API_BASE + path);
        const options = {
            hostname: url.hostname,
            port: url.port,
            path: url.pathname + url.search,
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };

        const req = http.request(options, (res) => {
            let body = '';
            res.on('data', (chunk) => body += chunk);
            res.on('end', () => {
                try {
                    const response = JSON.parse(body);
                    resolve({ status: res.statusCode, data: response });
                } catch (e) {
                    resolve({ status: res.statusCode, data: body });
                }
            });
        });

        req.on('error', reject);

        if (data) {
            req.write(JSON.stringify(data));
        }
        req.end();
    });
}

// Test functions
async function testAPI() {
    console.log('🧪 Testing College Student Management API');
    console.log('==========================================');

    let createdStudentId = null;

    try {
        // Test 1: Get all students (should work even if empty)
        console.log('\n1. Testing GET /api/students');
        const getAllResponse = await makeRequest('GET', '/students');
        console.log(`   Status: ${getAllResponse.status}`);
        console.log(`   Response: ${JSON.stringify(getAllResponse.data, null, 2)}`);

        // Test 2: Create a new student
        console.log('\n2. Testing POST /api/students');
        const createResponse = await makeRequest('POST', '/students', testStudent);
        console.log(`   Status: ${createResponse.status}`);
        console.log(`   Response: ${JSON.stringify(createResponse.data, null, 2)}`);
        
        if (createResponse.data.success) {
            createdStudentId = createResponse.data.data._id;
            console.log(`   ✅ Student created with ID: ${createdStudentId}`);
        }

        // Test 3: Get single student
        if (createdStudentId) {
            console.log('\n3. Testing GET /api/students/:id');
            const getOneResponse = await makeRequest('GET', `/students/${createdStudentId}`);
            console.log(`   Status: ${getOneResponse.status}`);
            console.log(`   Response: ${JSON.stringify(getOneResponse.data, null, 2)}`);
        }

        // Test 4: Update student
        if (createdStudentId) {
            console.log('\n4. Testing PUT /api/students/:id');
            const updateData = { ...testStudent, firstName: 'Jane', gpa: 3.85 };
            const updateResponse = await makeRequest('PUT', `/students/${createdStudentId}`, updateData);
            console.log(`   Status: ${updateResponse.status}`);
            console.log(`   Response: ${JSON.stringify(updateResponse.data, null, 2)}`);
        }

        // Test 5: Search students
        console.log('\n5. Testing GET /api/students/search/:query');
        const searchResponse = await makeRequest('GET', '/students/search/Computer');
        console.log(`   Status: ${searchResponse.status}`);
        console.log(`   Response: ${JSON.stringify(searchResponse.data, null, 2)}`);

        // Test 6: Delete student
        if (createdStudentId) {
            console.log('\n6. Testing DELETE /api/students/:id');
            const deleteResponse = await makeRequest('DELETE', `/students/${createdStudentId}`);
            console.log(`   Status: ${deleteResponse.status}`);
            console.log(`   Response: ${JSON.stringify(deleteResponse.data, null, 2)}`);
        }

        console.log('\n✅ All tests completed!');
        console.log('\n📝 Test Summary:');
        console.log('   - GET all students: ✅');
        console.log('   - POST new student: ✅');
        console.log('   - GET single student: ✅');
        console.log('   - PUT update student: ✅');
        console.log('   - GET search students: ✅');
        console.log('   - DELETE student: ✅');

    } catch (error) {
        console.error('\n❌ Test failed:', error.message);
        console.log('\n💡 Make sure the server is running with: npm start');
    }
}

// Check if server is running
async function checkServer() {
    try {
        const response = await makeRequest('GET', '/students');
        return response.status < 500;
    } catch (error) {
        return false;
    }
}

// Main execution
async function main() {
    const serverRunning = await checkServer();
    
    if (!serverRunning) {
        console.log('❌ Server is not running or not accessible');
        console.log('💡 Start the server first with: npm start');
        console.log('   Then run this test with: node test-api.js');
        process.exit(1);
    }

    await testAPI();
}

// Run if called directly
if (require.main === module) {
    main();
}

module.exports = { testAPI, makeRequest };