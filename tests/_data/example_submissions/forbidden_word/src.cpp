#include <stdio.h>
#include <stdlib.h>
using namespace std;

//adds two numbers
int main(int argc, char **argv){
	int one, two;
    system("/bin/true 2>errs");
	scanf("%d %d", &one, &two);
	printf("%d\n", one+two);
}
