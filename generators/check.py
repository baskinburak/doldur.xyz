f = open("data.json").read()
import json
a = json.loads(f)
print len(a)
for i in a:
	if len(i) != 3:
		print i
for i in a:
	if len(i["s"]) == 0:
		print i
