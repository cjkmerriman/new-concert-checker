# Popular Events Checker

A prototype of a tool for manually reviewing recently annunced events on Songkick. Displays events for top 1,000 artists, created in last 24 hrs. Ordered by popularity of artist.

## Getting Started

These instructions will get you a copy of the prototype up and running on your local machine so you can check it out.

### Prerequisites

The below need to be available on your local machine. In my case the PHP root is in my Sites folder.

```
* Node 6.9
* PHP 7
```

### Installing

Using command line tool, e.g. Terminal

```
1. Clone the repo to Sites folder or similar on your machine
1. Open server/check-popular.php and provide database credentials, save & close.
1. Change to app directory and run `npm install`
1. Connect to SK VPN
1. Enter `gulp serve`
```

A browser window should open running at localhost:9000 showing you events for review

![Event Checker](http://i68.tinypic.com/2qs7aip.png)

## How It Works

It get's all events from database created within the last 24 hours that are associated with acts with a popularity > 0.1 (approx. top 1,000 artists).

These are then displayed to the user fro approval. Approval status for each event is kept in local storage only.

### Missing

I never figured out how to determine the source from the event, that would be very valuable.

### End Goal

My end goal was to create a Chrome extension that would allow reviews to be crowd-sourced, with event approval status stored on the server.

## Authors

* **Callum Merriman** - *Concept*
