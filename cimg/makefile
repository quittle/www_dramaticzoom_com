#This makefile is to create an image processing program
#The output is an executable called 'newResizer'

#default produces a file with default compiler optimizations
#0 produces a file with no optimizations
#1 produces a file with some optimizations
#2 produces a file with a lot of optimizations
#3 produces a file with as many optimizations as it can
#3Plus produces a file with as many optimizations as it can and strips the executable of symbols. Shrinking it as much as possible

#Flags
#-L/usr/X11R6/lib (link to dynamic library)
#-lm (link to math library)
#-pthread (link to pthread library)
#-lX11 (link to -X11 library?)
CC=c++
INPUT=newResizer.cpp
OUTPUT=-o newResizer
MAGIC=`Magick++-config --cppflags --cxxflags --ldflags --libs`
FLAGS=-L/usr/X11R6/lib -lm -lpthread -lX11 $(MAGIC)

INITIAL_COMMAND=$(CC) $(INPUT) $(OUTPUT)

default:
	$(INITIAL_COMMAND) -O $(FLAGS)
0:
	$(INITIAL_COMMAND) -O0 $(FLAGS)
1:
	$(INITIAL_COMMAND) -O1 $(FLAGS)
2:
	$(INITIAL_COMMAND) -O2 $(FLAGS)
3:
	$(INITIAL_COMMAND) -O3 $(FLAGS)
3Plus:
	$(INITIAL_COMMAND) -O3 -s $(FLAGS)
