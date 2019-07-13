#include <iostream>
#include <Magick++.h>

using namespace std;
using namespace Magick;

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

/*
 * argv[0] - program name
 * argv[1] - input file
 * argv[2] - image background color
 * argv[3] - output file
 * argv[4] - min output size
 * argv[5] - max output size
 */
int main(int argc, const char* argv[]){
	const char* inputFile = argv[1];
	const Color bgColor = Color(argv[2]);
	const char* outputFile = argv[3];
	const int MIN_SIZE = atoi(argv[4]), MAX_SIZE = atoi(argv[5]);

	Image image;
	
	//Open image
	image.read(inputFile);

	//Resize to force one dimension to correct size
	std::cout << "Image dimensions before resize: " << image.rows() << "x" << image.columns() << "\n";

	const int GEOMETRY_LEN = 20;
	char minSizeResize[GEOMETRY_LEN], maxSizeResize[GEOMETRY_LEN], minSizeExtent[GEOMETRY_LEN], maxSizeCrop[GEOMETRY_LEN];

	//Make at least min size (for one dimension)
	sprintf(minSizeResize, "%dx%d<", MIN_SIZE, MIN_SIZE);
	image.resize(minSizeResize);
	
	//Make at most max size (for one dimension)
	if(image.rows() > image.columns()){
		sprintf(maxSizeResize, "%dx>", MAX_SIZE, MAX_SIZE);
	}else{
		sprintf(maxSizeResize, "x%d>", MAX_SIZE, MAX_SIZE);
	}
	image.resize(maxSizeResize);
	
	std::cout << "Image dimensions after resize: " << image.rows() << "x" << image.columns() << "\n";
	
	//Add padding to make it square
	if(min(image.rows(), image.columns()) < MIN_SIZE){
		sprintf(minSizeExtent, "%dx%d<", MIN_SIZE, MIN_SIZE);
		image.extent(minSizeExtent);
	}
	
	std::cout << "Image dimensions after forced sizing: " << image.rows() << "x" << image.columns() << "\n";
	
	//Crop the image to make it square (based on the shorter dimension)
	if(image.rows() > image.columns()){
		sprintf(maxSizeCrop, "%dx%d", image.columns(), image.columns());
	}else{
		sprintf(maxSizeCrop, "%dx%d", image.rows(), image.rows());
	}
	image.crop(maxSizeCrop);
	
	std::cout << "Image dimensions after cropping: " << image.rows() << "x" << image.columns() << "\n";
	
	//Apply background color after all the resizing to avoid artifacts from resizing
	Image canvas(Geometry(image.rows(), image.columns()), Color(bgColor));
	canvas.composite(image, 0, 0, OverCompositeOp);
	
	//Save the file.
	try{
		canvas.write(outputFile);
	}catch(Exception e){ // If the outputFile does not have a valid image extention, it will write the image as the type of the input and then rename it to the expected output filename
		//Give output file extension
		char extension[strlen(inputFile)];
		strcpy(extension, inputFile+lastIndex(inputFile, '.'));
		char output[strlen(outputFile) + strlen(extension) + 1];
		strcpy(output, outputFile);
		strcat(output, extension);
		
		canvas.write(output);
		
		//Rename output to remove extension
		rename(output, outputFile);
	}
	
	return 0;
}
