# Supermetrics API

The application needs PHP > 7.2 to run. You also need to have composer installed as I used it for dependency management.

## How to run the app
Run the following command in the root folder of the application in order to install the dependencies:
```bash
composer install
```
The application has a function that is generating the new token in every new session so no need to do anything else.
I've added the application as a virtual host in my local PC but you can try other ways to run it as well.

There are 4 query params that you can pass on to generate the stats:
- period : It can be either "month" or "week"
- identifier: It can be either "character" or "post"
- statOption: It can be either of the three - "average" or "longest" or "total"
- page (optional): This can be used if you need data for specific page of the API, otherwise if this is not provided, the data generated is for all pages.

## To get the tasks data:

1. Average character length of a post / month - yourhost.com?period=month&identifier=character&statOption=average
2. Longest post by character length / month - yourhost.com?period=month&identifier=post&statOption=longest
3. Total posts split by week - yourhost.com?period=week&identifier=post&statOption=total
4. Average number of posts per user / month - yourhost.com?period=month&identifier=post&statOption=average

#

The solution also supports:
#
5. Total characters by week - yourhost.com?period=week&identifier=character&statOption=total
6. Total characters by month - yourhost.com?period=month&identifier=character&statOption=total
