-- skip dark-canary if found
local m1 = ngx.re.match(ngx.var.request_uri, "^/dark-canary/")

-- increase counter if valid path
local regex = [[/(]]
regex = regex .. [[circleci(/.+)?(\.svg)?]]
regex = regex .. [[|composerlock(\.svg)?]]
regex = regex .. [[|d/(daily|monthly|total)(\.svg|\.png)?]]
regex = regex .. [[|dependents(\.svg)?]]
regex = regex .. [[|downloads(\.svg|\.png)?]]
regex = regex .. [[|gitattributes(\.svg)?]]
regex = regex .. [[|license(\.svg|\.png)?]]
regex = regex .. [[|suggesters(\.svg)?]]
regex = regex .. [[|v/(stable|unstable)(\.svg|\.png)?]]
regex = regex .. [[|version(\.svg|\.png)?]]
regex = regex .. [[)$]]

local m2 = ngx.re.match(ngx.var.request_uri, regex)
if m1 or not m2 then return end

local res = ngx.location.capture("/stats")

-- DARK CANARY
math.randomseed(os.time())
local odds = math.random(1, 100)
if odds <= dark_canary_threshold then
    local res_darkcanary = ngx.location.capture("/dark-canary" .. ngx.var.request_uri)
end
