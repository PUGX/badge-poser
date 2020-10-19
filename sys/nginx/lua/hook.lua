local regex = ""
regex = regex .. "/("
regex = regex .. "circleci"
regex = regex .. "|composerlock"
regex = regex .. "|d"
regex = regex .. "|daily"
regex = regex .. "|dependents"
regex = regex .. "|downloads"
regex = regex .. "|gitattributes"
regex = regex .. "|license"
regex = regex .. "|monthly"
regex = regex .. "|stable"
regex = regex .. "|suggesters"
regex = regex .. "|unstable"
regex = regex .. "|v"
regex = regex .. "|version"
regex = regex .. ")$"
local m = ngx.re.match(ngx.var.request_uri, regex)
if not m then return end

local res = ngx.location.capture("/stats")
