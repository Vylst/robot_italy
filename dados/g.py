import numpy as np
import matplotlib.pyplot as plt

rsum = [6,114,58,290,16,160,698,236,154,180,450,86,308,406,4,118,94,328,20,556,196,2,738,46,366,
	0,28,312,0,388,14,586,2,56,576,54,60,294,54,146,108,356,310,62,44,1164,680,674,724,786,744,872,156,
	806,216,0,82,622,18,134,274,460,40,0,2,550,128,90,204,178,140,442,62,14,2,944,8,300,4,4,624,254,366,40,438,12,0,64,868]

rsum[:] = [x / 90 for x in rsum]

plt.plot(rsum)
plt.show()


'''
l = [];
emotions = ['Angry', 'Disgust', 'Fear', 'Happy', 'Neutral', 'Sad', 'Surprise']
expressions = ['00','01','02','03','04','05','06','07','08','09','010','011','012',
		'10','11','12','13','14','15','16','17','18','19','110','111','112',
		'20','21','22','23','24','25','26','27','28','29','210','211','212',
		'30','31','32','33','34','35','36','37','38','39','310','311','312']

for i in range(9424):
	l.append([np.random.randint(0,7), np.random.randint(0,52), np.random.randint(1,2)])
	

print(emotions)
for j in range(len(expressions)):

	print(str(expressions[j])+',', end='')
	for i in range(len(emotions)):
		test_mix = [i, j, 1]
		
		cnt = l.count(test_mix)
		print(str(cnt)+',', end='')
		
		
	print('\n', end='')


'''
