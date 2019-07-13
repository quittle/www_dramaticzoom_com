/*/
 * How to call: ./resize "/path/to/file/image.png" "rgb(255,255,0)" 300 500 "path/to/output/resizedImage.png"
 * The second parameter may be rgb, rgba, #rrggbb, #rrggbbaa, cmyk, cmyka, hsl, hsla
 *
 * See http://www.imagemagick.org/RMagick/doc/imusage.html for color options and info for how geometry works for the methods used
 *
 * Note:
 * * The quotes around rgb() are needed because bash doesn't like parentheses
 * * Quotes around file paths with spaces, or if rgb() has spaces in it are necessary
/*/
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
 * argv[3] - min output size (in pixels)
 * argv[4] - max output size (in pixels)
 * argv[5] - output file
 * argv[6] - ignore or set to 0 if the image should be squared up, set to 1 to just bound it
 */
int main(int argc, const char* argv[]){
	InitializeMagick(*argv);

	const char* inputFile = argv[1];
	const Color bgColor = Color(argv[2]);
	const int MIN_SIZE = atoi(argv[3]), MAX_SIZE = atoi(argv[4]);
	const char* outputFile = argv[5];
	const char square = (argc == 7 && argv[6]) ? 1 : 0;
	
	printf("square: %d\n", square);

	Image image;
	char geometry[20];
	
	//Open image
	image.read(inputFile);

	//Make at least one dimension the min size
	sprintf(geometry, "%dx%d<", MIN_SIZE, MIN_SIZE);
	image.resize(geometry);
	
	//Resize so the smaller side is max size
	//COLSxROWS (which is WIDTHxHEIGHT)
	sprintf(geometry, image.rows() > image.columns() ? "%dx>" : "x%d>", MAX_SIZE);
	image.resize(geometry);
	
	//Add padding to make it square if the image is too small
	if(min(image.rows(), image.columns()) < MIN_SIZE){
		sprintf(geometry, "%dx%d<", MIN_SIZE, MIN_SIZE);
		image.extent(geometry);
	}
	
	//Crop the image to make it square (based on the shorter dimension)
	const int width = image.columns(), height = image.rows(),
				size = min(width, height),
				xOffset = (int) (width-size)/2,
				yOffset = (int) (height-size)/2;
	
	sprintf(geometry, "%dx%d+%d+%d", size, size, xOffset, yOffset);
	image.crop(geometry);
	
	//Apply background color after all the resizing to avoid artifacts from resizing
	Image canvas(Geometry(image.rows(), image.columns()), Color(bgColor));
	canvas.composite(image, 0, 0, OverCompositeOp);
	
	//Save the file.
	try{
		canvas.write(outputFile);
	}catch(Exception e){ // If the outputFile does not have a valid image extension, it will fail so use the one given by the input source
		//Copy the extension from the input file into extension
		char extension[strlen(inputFile)+1];
		strcpy(extension, inputFile+lastIndex(inputFile, '.'));
		
		//Create an output file name with the original name maximized
		char output[strlen(outputFile) + strlen(extension) + 1];
		strcpy(output, outputFile);
		strcat(output, extension);
		
		//Save the file again, now that you know it has a file extension
		canvas.write(output);
		
		//Rename output to the requested output file name. NOTE: This could cause issues if there is another file with the output name, but with the extention in existence. For instance, if you want to output to "outputFile" and a file called "outputFile.png" exists, it will overwrite it.
		rename(output, outputFile);
	}
	printf("Done!\n");
	return 0;
}
