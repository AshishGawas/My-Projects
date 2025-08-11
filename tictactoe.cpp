// TIC TAC TOE GAME
#include <iostream>
using namespace std;

char board[3][3] = {{'1', '2', '3'}, {'4', '5', '6'}, {'7', '8', '9'}};

void displayBoard() {
    cout << " " << board[0][0] << " | " << board[0][1] << " | " << board[0][2] << endl;
    cout << "---+---+---" << endl;
    cout << " " << board[1][0] << " | " << board[1][1] << " | " << board[1][2] << endl;
    cout << "---+---+---" << endl;
    cout << " " << board[2][0] << " | " << board[2][1] << " | " << board[2][2] << endl;
}

bool checkWin(char c) {
    // This will check for same characters in rows and columns
    for (int i = 0; i < 3; i++) {
        if (board[i][0] == c && board[i][1] == c && board[i][2] == c) return true;
        if (board[0][i] == c && board[1][i] == c && board[2][i] == c) return true;
    }
    // This will check for same characters in diagonals
    if ((board[0][0] == c && board[1][1] == c && board[2][2] == c) ||
        (board[0][2] == c && board[1][1] == c && board[2][0] == c)) {
        return true;
    }
    return false;
}

bool checkDraw() {
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            if (board[i][j] != 'X' && board[i][j] != 'O') {
                return false;
            }
        }
    }
    return true;
}

int main() {
    char currentPlayer = 'X';

    while (true) {
        displayBoard();
        int move;
        cout << "Player " << currentPlayer << ", enter your move (1-9): ";
        cin >> move;

        // Input validation for move
        if (move < 1 || move > 9) {
            cout << "Invalid move, please enter a number between 1 and 9." << endl;
            continue;
        }

        int row = (move - 1) / 3;
        int col = (move - 1) % 3;

        if (board[row][col] == 'X' || board[row][col] == 'O') {
            cout << "That cell is already occupied, try again." << endl;
            continue;
        }

        board[row][col] = currentPlayer;

        if (checkWin(currentPlayer)) {
            displayBoard();
            cout << "Player " << currentPlayer << " is the WINNER " << endl;
            break;
        }

        if (checkDraw()) {
            displayBoard();
            cout << "It's a DRAW!" << endl;
            break;
        }

        currentPlayer = (currentPlayer == 'X') ? 'O' : 'X';
    }

    return 0;
}