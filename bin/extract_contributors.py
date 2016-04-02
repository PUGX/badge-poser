from pygithub3 import Github
from jinja2 import Template
import datetime

gh = Github()
try:
  result1 = gh.repos.list_contributors(user='PUGX',repo='badge-poser')
except:
  print "error contacting github PUGX/badge-poser try later"
  exit(1)

try:
  result2 = gh.repos.list_contributors(user='badges',repo='poser')
except:
  print "error contacting github on badges/poser try later"
  exit(2)


hash = {}
result = []
for contributors in result1:
    for user in contributors:
        if user.login not in hash.keys():
            result.append(user)
        hash[user.login] = 1

for contributors in result2:
    for user in contributors:
        if user.login not in hash.keys():
            result.append(user)
        hash[user.login] = 1

s = """
{% for user in contributor %}
<li class="span2">
    <a href="{{ user.html_url }}" target="_blank" title="{{ user.login }} contributions">{{ user.login }}</a>
    <a href="{{ user.html_url }}" target="_blank" title="{{ user.login }} contributions"><img src="{{ user.avatar_url }}" alt="{{ user.login }}'s avatar"></a>
</li>
{% endfor %}
"""

print "{# created "+str(datetime.date.today())+" #}"

template = Template(s)
print template.render(contributor=result)

