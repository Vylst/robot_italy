import numpy as np

from tensorflow import keras


eyes = keras.models.load_model('2021-09-19_eyes.h5')
mouth = keras.models.load_model('2021-09-19_mouth.h5')


emotions = ['Angry', 'Disgust', 'Fear', 'Happy', 'Neutral', 'Sad', 'Surprise']
occurrences = [1749, 902, 1009, 442, 854, 1654, 1265]

acceptable_configs = [[1,1],[1,6],[1,11],[2,1],[2,2],[2,6],[2,11],[2,12],[3,1],[3,6],[3,11]]


number_of_states = 2

full_list = []


for i in range(len(occurrences)):
	n_int = occurrences[i]
	user_emotion = i
	s = 0
	
	print("Doing emotion: ", emotions[i])
	
	
	
	counter_list = [0,0,0,0,0,0,0]
	for j in range(n_int):
		
		a = [0, 0]
		while(a not in acceptable_configs):
			a_eyes = np.argmax(eyes.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
			a_mouth = np.argmax(mouth.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
			a = [a_eyes, a_mouth]
			
		ind = acceptable_configs.index(a)
		counter_list[ind] = counter_list[ind] + 1
			
			
			
	full_list.append(counter_list)	
			
			
print(full_list)			
			
			
			
			
			
