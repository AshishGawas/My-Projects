#include <iostream>
#include <cstdlib>
#include <ctime>

using namespace std;

int main() {
    srand(time(0)); //for seeds or starts the random number generator in current time
    int secretNumber = rand() % 100 + 1; // it will generate random numbers from 0-99 and 1-100
    int guess;
    int attempts = 0;

    cout << "Welcome To The Guess The Number Game" << endl;
    cout << "I have picked a random number between 1 and 100 " << endl;
    cout << "Can You Guess that number " << endl;


do {
        cout << "Enter your Guess Number: ";
        cin >> guess;
        attempts++;

        if (guess < secretNumber) {
            cout << " ): The number is too small! Try again." << endl;
        } else if (guess > secretNumber) {
            cout << " ): The number is too big! Try again." << endl;
        } else {
            cout << " (: Congratulations! You've guessed the number in " << attempts << " attempts." << endl;
        }    
    } while (guess != secretNumber);

    return 0;
}