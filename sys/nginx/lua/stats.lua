local redis = require "resty.redis"
local red = redis:new()
local ok, err = red:connect(redis_host, 6379)
if ok then
    ok, err = red:incr("STAT.TOTAL")
    ngx.say(err)
end
