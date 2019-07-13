#include "CImg.h"
#include <iostream>
using namespace cimg_library;

#define MIN_SIZE 200
#define MAX_SIZE 1500


int min(const int m,const int n){
	return m<n?m:n;
}
int lastIndex(const char* haystack, const char needle){
	for(int i=strlen(haystack)-1;i>=0;i--){
		if(haystack[i] == needle)
			return i;
	}
	return -1;
}

int main(int argc, const char* argv[]){
	std::cout << "input: " << argv[1] << "\n";

	//Open image
	CImg<unsigned char> image(argv[1]);
	
	//Enlarge image to make it correct size
	if(image.width()<MIN_SIZE){
		int scale = (int)((MIN_SIZE/(float)image.width()) * -100);
		image.resize(scale, scale, -100, -100, 5);
	}
	if(image.height()<MIN_SIZE){
		int scale = (int)((MIN_SIZE/(float)image.height()) * -100);
		std::cout << scale << "\n";
		image.resize(scale, scale, -100, -100, 5);
		std::cout << image.width() << " " << image.height() << "\n";
	}
	
	//Shrink the larger size to the correct dimensions
	if(image.width()>MAX_SIZE && image.height()>image.width()){
		int scale = (int)(MAX_SIZE/((float)image.width()) * -100);
		std::cout << scale << "\n";
		image.resize(scale, scale, -100, -100, 5);
	} else if(image.height()>MAX_SIZE){
		int scale = (int)(MAX_SIZE/((float)image.height()) * -100);
		std::cout << scale << "\n";
		image.resize(scale, scale, -100, -100, 5);
	}
	
	//Crop to the correct size
	int size = min(image.width(), image.height())-1; //For whatever reason CImg crops 1px too large
	int x0 = (int) (image.width()-size)/2;
	int y0 = (int) (image.height()-size)/2;
	int x1 = x0 + size;
	int y1 = y0 + size;
	image.crop(x0,y0,x1,y1);
	
	//Give output file extension
	char extension[strlen(argv[1])];
	strcpy(extension, argv[1]+lastIndex(argv[1], '.'));
	char output[strlen(argv[2]) + strlen(extension) + 1];
	strcpy(output, argv[2]);
	strcat(output, extension);
	
	//Save image
	std::cout << "output: " << output << "\n";
	image.save(output);
	
	//Rename output to remove extension
	rename(output, argv[2]);
	
	return 0;
}
