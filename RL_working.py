import keras
import numpy as np
import matplotlib.pylab as plt
from keras.models import Sequential, Model
from keras.layers import Input, InputLayer, Dense, Concatenate

emotions = ['Angry', 'Disgust', 'Fear', 'Happy', 'Neutral', 'Sad', 'Surprise']
expressions = [[3,8], [0,5], [2,11], [0,0], [2,6], [1,1], [2,12]]


def next_state_f(a, user_emotion, steps, state):

	am_i_done = False
	reward = 0

	if(a[0] == expressions[user_emotion][0] and a[1] == expressions[user_emotion][1]): 
		new_state = 1
		reward = 2
		if(state == 1):
			reward = 10
	else:
		new_state = 0
		
	#Worked with 250 steps per 1000 episodes, and 25 steps per 10000 episodes
	if(steps == 250):
		am_i_done = True
	
	return (new_state, reward, am_i_done)


#base_model = Sequential()
#base_model.add(Dense(9, activation='relu', kernel_initializer='he_uniform', name='hidden1', input_dim=7))
#base_model.add(Dense(27, activation='relu', name='hidden2'))
#base_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])

sgd = keras.optimizers.SGD(learning_rate=0.001)

state_input = Input((2,))
user_emotion_input = Input((7,))
conc_input = Concatenate()([state_input, user_emotion_input])
base_hidden1 = Dense(9, activation='relu', kernel_initializer='he_uniform', name='hidden1')(conc_input)
base_output = Dense(27, activation='relu', name='hidden2')(base_hidden1)
base_model = Model(inputs=[state_input, user_emotion_input], outputs=base_output)
base_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])
base_model.summary()


eye_input = Input((27,))
eye_hidden1 = Dense(4, activation='sigmoid', kernel_initializer='he_uniform', name='eye_hidden1')(eye_input)
eye_model = Model(inputs=eye_input, outputs=eye_hidden1)
eye_output = eye_model(base_output)
eyes = Model(inputs=[state_input, user_emotion_input], outputs=eye_output)
eyes.compile(loss='mse', optimizer='adam', metrics=['mae'])

mouth_input = Input((27,))
mouth_hidden1 = Dense(13, activation='sigmoid', kernel_initializer='he_uniform', name='mouth_hidden1')(mouth_input)
mouth_model = Model(inputs=mouth_input, outputs=mouth_hidden1)
mouth_output = mouth_model(base_output)
mouth = Model(inputs=[state_input, user_emotion_input], outputs=mouth_output)
mouth.compile(loss='mse', optimizer='adam', metrics=['mae'])

#eye_model = Sequential()
#eye_model.add(Dense(4, activation='sigmoid', kernel_initializer='he_uniform', name='eye_out', input_dim=27))
#eye_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])

#mouth_model = Sequential()
#mouth_model.add(Dense(13, activation='sigmoid', kernel_initializer='he_uniform', name='mouth_out', input_dim=27))
#mouth_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])

#eyes = Sequential()
#eyes.add(base_model)
#eyes.add(eye_model)
#eyes.compile(loss='mse', optimizer='adam')
#mouth = Sequential()
#mouth.add(base_model)
#mouth.add(mouth_model)
#mouth.compile(loss='mse', optimizer='adam')

eyes.summary()
keras.utils.plot_model(eyes, to_file='eyes.png')
mouth.summary()
keras.utils.plot_model(mouth, to_file='mouth.png')




eyes_value = 0
mouth_value = 0
a = [eyes_value, mouth_value]

number_of_states = 2

# now execute the q learning
num_episodes = 1000
y = 0.095
eps = 0.5
decay_factor = 0.999
r_avg_list = []
iteration_cntr = 0
temporal_array = []
rrrr = []

for i in range(num_episodes):
	
	s = 0
	
	eps *= decay_factor
	

	print("Episode {} of {}".format(i + 1, num_episodes))
	print('\n')
	done = False
	r_sum = 0
	window = []
	iteration_cntr = 0
	
	user_emotion = np.random.randint(0,7)
	
	while not done:
	
		#if(len(window) == 4):
		#	window.pop(0)
	
		#Choose action (a), either it will be a random value or be chosen based on the prediction of 
		if np.random.random() < eps:
			a = [np.random.randint(0, 4), np.random.randint(0, 13)]
		else:
			#Predict the Q values for each possible action. Choose the action corresponding to highest Q-value
			a[0] = np.argmax(eyes.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
			a[1] = np.argmax(mouth.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
			

		
		#window = [[a[0], a[1]]] #window + [[a[0], a[1]]]
			
		print("User: ", emotions[user_emotion])	
		print("My reaction: (", a[0], ',', a[1], ')')
		print('\n')

		new_s, r, done = next_state_f(a, user_emotion, iteration_cntr, s)

		eye_target = r + y * np.max(eyes.predict( [ np.identity(number_of_states)[new_s:new_s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
		mouth_target = r + y * np.max(mouth.predict( [ np.identity(number_of_states)[new_s:new_s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))

		eye_target_vec = eyes.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] )[0]
		mouth_target_vec = mouth.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] )[0]


		eye_target_vec[a[0]] = eye_target
		mouth_target_vec[a[1]] = mouth_target


		eyes.fit( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] , eye_target_vec.reshape(1, -1), epochs=1, verbose=0)
		mouth.fit( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] , mouth_target_vec.reshape(1, -1), epochs=1, verbose=0)


		#Update state
		s = new_s
		#Update cumulative reward
		r_sum += r
		
		
		iteration_cntr = iteration_cntr + 1
	
		#print('done:', done)
	
	#rrrr = rrrr + [r_sum]
	#temporal_array = temporal_array + [[i, iteration_cntr]]
		
	#Save average reward per episode
	r_avg_list.append(r_sum / num_episodes)


#rrrr = np.array(rrrr)
#temporal_array = np.array(temporal_array)
#x_ax = temporal_array[:,0]
#y_ax = temporal_array[:,1]

#fig, axs = plt.subplots(2)
#axs[0].plot(x_ax,y_ax)
#axs[1].plot(x_ax,r_avg_list)
#axs[0].set_title('Number of fittings')
#axs[1].set_title('Cumulative Reward')
#plt.savefig('two_state_test.png')
#plt.show()

#fig.savefig('safeguard.png')


plt.plot(r_avg_list)
plt.ylabel('Average reward per game')
plt.savefig('r_avg_list.png')
































#plt.xlabel('Number of games')
#plt.show()
#for i in range(3):
#	print("State {} - action {}".format(i, model.predict(np.identity(3)[i:i + 1])))














