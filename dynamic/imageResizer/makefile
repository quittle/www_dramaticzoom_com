#This makefile is to create an image processing program
#The output is an executable called 'newResizer'

#default - produces a file with default compiler optimizations
#O       - produces a file with as many optimizations as it can and strips the executable of symbols. Shrinking it as much as possible

#Flags
#-L/usr/X11R6/lib (link to dynamic library)
#-lm (link to math library)
#-pthread (link to pthread library)
#-lX11 (link to -X11 library?)
CC=c++
INPUT=newResizer.cpp
OUTPUT=resize
MAGIC=`Magick++-config --cppflags --cxxflags --ldflags --libs`
FLAGS=-L/usr/X11R6/lib -lm -lpthread -lX11 $(MAGIC)
EXECUTABLE=chmod a+x $(OUTPUT)

INITIAL_COMMAND=$(CC) $(INPUT) -o $(OUTPUT)

default:
	$(INITIAL_COMMAND) -O0 $(FLAGS)
	$(EXECUTABLE)
O:
	$(INITIAL_COMMAND) -O3 -s $(FLAGS)
	$(EXECUTABLE)
