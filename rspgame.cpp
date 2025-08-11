/*
    Paper beats Rock
    Same choices = Draw
*/

#include <iostream>   // For input and output
#include <cstdlib>    // For rand() and srand() for generating random number
#include <ctime>      // For time() function

using namespace std;

int main()
{
    int player, computer;   // choices of user and computer
    char choice;            // To decide if the user wants to play again

    do {
        srand(time(0)); // seed random generator with current time
        computer = rand() % 3 + 1; // computer generates random number between 1 to 3

        // Display choices to player
        cout << "\n1. Rock \n2. Paper \n3. Scissors\n";
        cout << " Choose Your move (1-3):";
        cin >> player;

        // show what the computer picked
        cout << "Computer chose: " << computer << endl;

        // Decide winner
        if (player == computer) {
            cout << "It's a draw" << endl;
        }
        else if ((player == 1 && computer == 3) ||
                 (player == 2 && computer == 1) ||
                 (player == 3 && computer == 2)) {
            cout << "You won" << endl;
        }
        else {
            cout << "You loss " << endl;
        }

        cout << "\nWants to play again?(Y/N):" << endl;
        cin >> choice;
    } while (choice == 'Y' || choice == 'y');

    return 0;
}
