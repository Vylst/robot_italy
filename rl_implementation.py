import keras
import numpy as np
from keras.models import Sequential, Model
from keras.layers import Input, InputLayer, Dense


def next_state_f(a, window):

	#Emotion recognition module
	#face_emotion
	
	#User feedback
	while True:
		try:
			user_feedback = int(input('Feedback: '))
			if user_feedback < 0 or user_feedback > 2:
				raise ValueError #this will send it to the print message and back to the input option
			break
		except ValueError:
			print("Invalid integer. The number must be in the range of -1 to 1.")

	#Define reward function
	reward = (user_feedback-1)*10 + np.random.randint(-9,9)

	new_state = user_feedback

	print(len(window))
	if( (len(window) == 4) and (window[0][1] == window[1][1] == window[2][1] == window[3][1] == 0) ):
		am_i_done = True
		print(am_i_done)
	else:
		am_i_done = False
	
	return (new_state, reward, am_i_done)


base_model = Sequential()
base_model.add(Dense(9, activation='relu', kernel_initializer='he_uniform', name='hidden1', input_dim=3))
base_model.add(Dense(27, activation='relu', name='hidden2'))
base_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])

eye_model = Sequential()
eye_model.add(Dense(3, activation='sigmoid', kernel_initializer='he_uniform', name='eye_out', input_dim=27))
eye_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])

mouth_model = Sequential()
mouth_model.add(Dense(3, activation='sigmoid', kernel_initializer='he_uniform', name='mouth_out', input_dim=27))
mouth_model.compile(loss='mse', optimizer='adam', metrics=['accuracy'])

eyes = Sequential()
eyes.add(base_model)
eyes.add(eye_model)
eyes.compile(loss='mse', optimizer='adam')
mouth = Sequential()
mouth.add(base_model)
mouth.add(mouth_model)
mouth.compile(loss='mse', optimizer='adam')

eyes.summary()
keras.utils.plot_model(eyes, to_file='eyes.png')
mouth.summary()
keras.utils.plot_model(mouth, to_file='mouth.png')

'''
encoded_state = Input((3,), name='state')
hidden1 = Dense(9, activation='relu', name='hidden1')(encoded_state)
hidden2 = Dense(27, activation='relu', name='hidden2')(hidden1)
eyes_h1 = Dense(6, activation='sigmoid', name='eyes')(hidden2)
mouth_h1 = Dense(16, activation='sigmoid', name='mouth')(hidden2)
model = Model(inputs=encoded_state, outputs=[eyes_h1, mouth_h1])

model.summary()
model.compile(loss={'eyes': 'mse','mouth': 'mse'}, optimizer='adam', metrics={'eyes':tf.keras.metrics.RootMeanSquaredError(), 'mouth':tf.keras.metrics.RootMeanSquaredError()})
keras.utils.plot_model(model, to_file='multiple_outputs.png')
'''



eyes_value = 0
mouth_value = 0
a = [eyes_value, mouth_value]

# now execute the q learning
num_episodes = 1000
y = 0.95
eps = 0.5
decay_factor = 0.999
r_avg_list = []


for i in range(num_episodes):
	
	s = 0
	
	eps *= decay_factor
	

	print("Episode {} of {}".format(i + 1, num_episodes))
	print('\n')
	done = False
	r_sum = 0
	window = []
	
	while not done:
	
		if(len(window) == 4):
			window.pop(0)
	
		#Choose action (a), either it will be a random value or be chosen based on the prediction of 
		if np.random.random() < eps:
			a = [np.random.randint(0, 3), np.random.randint(0, 3)]
		else:
			#Predict the Q values for each possible action. Choose the action corresponding to highest Q-value
			a[0] = np.argmax(eyes.predict(np.identity(3)[s:s + 1]))
			a[1] = np.argmax(mouth.predict(np.identity(3)[s:s + 1]))
			

		
		window = window + [[a[0], a[1]]]
			
			
		print("--My reaction--")
		print("Eyes: ", a[0])
		print("Mouth: ", a[1])

		new_s, r, done = next_state_f(a, window)

		eye_target = r + y * np.max(eyes.predict(np.identity(3)[new_s:new_s + 1]))
		mouth_target = r + y * np.max(mouth.predict(np.identity(3)[new_s:new_s + 1]))

		eye_target_vec = eyes.predict(np.identity(3)[s:s + 1])[0]
		mouth_target_vec = mouth.predict(np.identity(3)[s:s + 1])[0]


		eye_target_vec[a[0]] = eye_target
		mouth_target_vec[a[1]] = mouth_target


		eyes.fit(np.identity(3)[s:s + 1], eye_target_vec.reshape(1, -1), epochs=1, verbose=0)
		mouth.fit(np.identity(3)[s:s + 1], mouth_target_vec.reshape(1, -1), epochs=1, verbose=0)


		#Update state
		s = new_s
		#Update cumulative reward
		r_sum += r
	
		print('done:', done)
		
	#Save average reward per episode
	r_avg_list.append(r_sum / 1000)

plt.plot(r_avg_list)
plt.ylabel('Average reward per game')
plt.xlabel('Number of games')
plt.show()
for i in range(3):
	print("State {} - action {}".format(i, model.predict(np.identity(3)[i:i + 1])))














