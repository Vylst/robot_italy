import keras
import numpy as np
import time
import asyncio
import websockets
import json
import logging
import functools
import ssl
import pathlib
import matplotlib.pylab as plt
import datetime

from keras.models import Sequential, Model
from keras.layers import Input, InputLayer, Dense, Concatenate

emotions = ['Angry', 'Disgust', 'Fear', 'Happy', 'Neutral', 'Sad', 'Surprise']
expressions = [[3,8], [0,5], [2,11], [0,0], [2,6], [1,1], [2,12]]	#This is here just for my own clarity

eyes_value = 0
mouth_value = 0
a = [eyes_value, mouth_value]
number_of_states = 2

y = 0.095
eps = 0.5
decay_factor = 0.999
r_avg_list = []
con_cntr = 0
iteration_global_cntr = 0
user_list = []

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

eyes.summary()
keras.utils.plot_model(eyes, to_file='eyes.png')
mouth.summary()
keras.utils.plot_model(mouth, to_file='mouth.png')


def next_state_f(a, user_emotion, state, user_feedback):

	reward = 0

	#User says coherent
	if(user_feedback == 1):
		new_state = 1
		reward = 2
		if(state == 1):
			reward = 10
	#User says incoherent
	else:
		new_state = 0
	
	return (new_state, reward)

async def emotion(websocket, path):

	rsumfile = open("result_files/rsum.txt", "a")
	datafile = open("result_files/datafile.txt", "a")
	global eps
	global a
	steps = 0
	global r_avg_list
	global con_cntr		#Connection counter
	global iteration_global_cntr	#Global step counter
	global user_list		#List of connected users
	
	con_cntr = con_cntr + 1	#new connection
	
	
	test_id = np.random.randint(0,1000)
	while(test_id in user_list):
		test_id = np.random.randint(0,1000)
	
	

	
	
	
	while(True):

		#Set maximum number of concurrent users
		if(len(user_list) < 2):
			user_list = user_list + [test_id]
			await websocket.send("go")
		else:
			await websocket.send("full")
			break

		s = 0
		print("HI")
		#print("Episode {} of {}".format(i + 1, num_episodes))
		#print('\n')
		done = False
		r_sum = 0
		
		user_emotion = np.random.randint(0,7)
		try:
			print("HI")
			while not done:


				#Choose action (a), either it will be a random value or be chosen based on the prediction of 
				if np.random.random() < eps:
					a = [np.random.randint(0, 4), np.random.randint(0, 13)]
				else:
					#Predict the Q values for each possible action. Choose the action corresponding to highest Q-value
					a[0] = np.argmax(eyes.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
					a[1] = np.argmax(mouth.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
			
				
				print("SENT - ", test_id)
				print("Emotion: ", emotions[user_emotion])	
				print("Reaction: (", a[0], ',', a[1], ')')
				print('\n')
			
			
				success = await websocket.send(str(user_emotion) + ',' + str(a[0]) + ',' + str(a[1]))
				message = await websocket.recv();

				feedback = int(message.split(',')[3])
				a[0] = int(message.split(',')[1])
				a[1] = int(message.split(',')[2])
				user_emotion = int(message.split(',')[0])
				


				new_s, r = next_state_f(a, user_emotion, s, feedback)

				eye_target = r + y * np.max(eyes.predict( [ np.identity(number_of_states)[new_s:new_s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))
				mouth_target = r + y * np.max(mouth.predict( [ np.identity(number_of_states)[new_s:new_s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] ))

				eye_target_vec = eyes.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] )[0]
				mouth_target_vec = mouth.predict( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] )[0]


				eye_target_vec[a[0]] = eye_target
				mouth_target_vec[a[1]] = mouth_target


				print(test_id , " - Training with: ")
				print("Emotion: ", emotions[user_emotion])	
				print("Reaction: (", a[0], ',', a[1], ')')
				print("Feedback: ", feedback)
				print('\n')

				eyes.fit( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] , eye_target_vec.reshape(1, -1), epochs=1, verbose=0)
				mouth.fit( [ np.identity(number_of_states)[s:s + 1], np.identity(7)[user_emotion:user_emotion + 1] ] , mouth_target_vec.reshape(1, -1), epochs=1, verbose=0)


				#Update state
				s = new_s
				#Update cumulative reward
				r_sum += r
				
				steps = steps + 1
		
				if(steps % 250 == 0):
					done = True
					
				timestamp = datetime.datetime.now()
				datastring = "Time>" + str(timestamp) + ",State>" + emotions[user_emotion] + ",Eyes>" + str(a[0]) + ",Mouth>" + str(a[1]) + ",UF>" + str(feedback) + '\n'
				datafile.write(datastring)
				

				#Increment global step counter (independent from number of connections)				
				iteration_global_cntr = iteration_global_cntr + 1

				#Every 250 steps (regardless of number of connections), decay the eps
				if(iteration_global_cntr % 250 == 0):
					eps *= decay_factor
					print("EPS:", eps)
					#Save cumulative average reward
					#r_avg_list.append(r_sum)
					rsumfile.write(str(r_sum) + '\n')
				
		
					

		except websockets.exceptions.ConnectionClosed:
		
			
			name = datetime.datetime.today().strftime('%Y-%m-%d')
			eyes.save('models/' + name + '_eyes.h5')
			mouth.save('models/' + name + '_mouth.h5')
		
			user_list.remove(test_id)
			print("Client ", test_id, " disconnected. Do cleanup")
			break
			await asyncio.sleep(random.random() * 3)



		


#ssl_context = ssl.create_default_context(ssl.Purpose.CLIENT_AUTH)
#ssl_context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
#localhost_pem = pathlib.Path(__file__).with_name("localhost.pem")
#ssl_context.load_cert_chain("localhost.pem")


#Os portos abertos foram o 6543, o 80 e acho que o 443

start_server = websockets.serve(emotion, "193.136.230.116", 6543)#, ssl=ssl_context)
loop = asyncio.get_event_loop()
loop.run_until_complete(start_server)
loop.run_forever()

























