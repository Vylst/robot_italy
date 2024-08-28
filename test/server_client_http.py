#!/usr/bin/env python

# WS server that sends messages at random intervals

import asyncio
import json
import logging
import websockets

logging.basicConfig()

STATE = {"value": 0}

USERS = set()


def state_event():
	return json.dumps({"type": "state", **STATE})


def users_event():
	return json.dumps({"type": "users", "count": len(USERS)})


async def notify_state():
	if USERS:  # asyncio.wait doesn't accept an empty list
		message = state_event()
		await asyncio.wait([user.send(message) for user in USERS])


async def notify_users():
	if USERS:  # asyncio.wait doesn't accept an empty list
		message = users_event()
		await asyncio.wait([user.send(message) for user in USERS])


async def register(websocket):
	USERS.add(websocket)
	#await notify_users()


async def unregister(websocket):
	USERS.remove(websocket)
	#await notify_users()


async def counter(websocket, path):
	# register(websocket) sends user_event() to websocket
	await register(websocket)
	try:
		#await websocket.send(state_event())
		async for message in websocket:
			print(message)
			data = json.loads(message)
			if data["action"] == "reward":
				reward= int(data["value"])
				#await notify_state()
				await asyncio.wait([user.send(message) for user in USERS])
			elif data["action"] == "expression":
				expression= data["value"]
				print(data["value"])
				await asyncio.wait([user.send(message) for user in USERS])

				#await asyncio.wait([user.send(message) for user in USERS])

				#await notify_state()
			else:
				logging.error("unsupported event: %s", data)
	finally:
		await unregister(websocket)


start_server = websockets.serve(counter, "localhost", 6789)

asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()