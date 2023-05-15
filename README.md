# Logger exercise

This repo contains my solution for the following exercise:


We want to build a small Logger library in the programming language of our choice. This will be a library used by other development teams in the company, so we need to build it as such and it needs to be generic and open enough.

- First, we want to implement functionality to log a string to console.
- Then, we want to add support for different log levels: Debug, Info, Warning, Error. While clients of the library can send any of these log levels, we should be able to configure which severity level is accepted at the moment.
  - The log levels order is: Debug, Info, Warning, Error.
  - For example, if we configure that the minimum accepted log level is Warning, we will not handle Debug and Info logs.
  - This is configured in runtime.
- Now, we want to add log targets. How can we change and best model our Logger library so we can send all received logs to different targets. Our first target was console, but now we want e-mail, file system, server APIs, etc. Do not code the actual implementation of these targets (just log something that can be differentiated in console). This is more of a model exercise.
- Finally, how can we configure our minimum log levels per target? For example, errors only going to e-mail but console prints everything, etc. Also needs to be configurable in runtime.

This is a simple exercise. So we want you to consider a few things:

- Consider performance. A logger could go through some very heavy usage depending on which team uses it.
- Proper management in a multi threading environment
- Have clean code and structure

## My solution

The solution I've coded consists on a `Logger` class that manages all the logging of the application. This class holds different instances of `AbstractLogger`, which will ultimately allow us to create different kind of loggers for different purposes (file, e-mail, slack, console...) just by creating a class and registering it on runtime:

```php
$logger = new Logger();
$logger->addLogger(new ConsoleLogger(LogLevel::DEBUG));
$logger->addLogger(new FileLogger(LogLevel::INFO));
$logger->addLogger(new EmailLogger(LogLevel::WARNING));
$logger->addLogger(new SlackLogger(LogLevel::ERROR));
```

Each of these targets can have a different log level, so they will only be used when a specific logging is of this level or above. In the previous example, running the line `$logger->info('This is an info message')` would only trigger the `ConsoleLogger` and the `FileLogger`.

### How to create a new logger

Creating a new logger is quite easy right now. Just create a new class extending the `AbstractLogger` and implement the `protected function logAction(string $message, LogLevel $logLevel, ?array $data = null)` method:

```php
class ConsoleLogger extends AbstractLogger
{
    public function __construct(LogLevel $minLogLevel)
    {
        parent::__construct('Console', $minLogLevel);
    }

    protected function logAction(string $message, LogLevel $logLevel, ?array $data = null)
    {
        echo $this->buildLogMessage($message, $logLevel, $data) . PHP_EOL;
    }
}
```

The `buildLogMessage` function returns a message in the following format:

`{LOGGER_NAME}: {DATE_ISO_FORMAT} [{LOG_LEVEL}] {MESSAGE} {DATA_IN_JSON}`

For example, executing `$logger->info('Hello world!')` with the `ConsoleLogger` would log the following:

`Console: 2023-05-10 18:08:00 [info] Hello world!`

## Extra considerations

### Open to modification
All the classes are non-final and, thus, extendable and open to modification. Methods are protected and not private so they can also be overridden if needed.

### Give us your own ideas on how to improve this
I come from the Laravel world and, thanks to the use of PHP traits and interfaces, it's quite easy to make something *queueable*. I'm thinking of using a logging queue that processes in the background since some logging targets will be quite time consuming (i.e. sending a POST request to Slack or connecting via SMTP to an email server). Leaving this processing to a queue could make our logging faster in the main thread, while the processing would be taken care when there's enough capacity for it. I'm thinking of something like:

```php
class SlackLogger extends QueueableLogger {
    use SendsSlackMessages;

    public function __construct(LogLevel $minLogLevel)
    {
        parent::__construct('Slack', $minLogLevel);
    }

    protected function logAction(string $message, LogLevel $logLevel, ?array $data = null) {
        $this->sendSlackMessage($this->buildLogMessage($message, $logLevel, $data));
    }
}
```

In this hypothetical `QueueableLogger`, when the log function is called, a new job would be dispatched to a processing queue and, when the time comes, the `logAction` method would be called and the Slack message would be sent.

Also, not handling the failure case on the `$this->sendSlackMessage()` call is made on purpose since I would expect that, if the job fails executing, there's some retrying engine that would retry the same job after some seconds / minutes.

### Any improvements to the code that could simplify our Big O notation of every method? Any ways to remove loops, if, switches, etc?
There's only one loop, which iterates through the registered logger targets. The rest of the code is pretty much O(1), except for some internal methods that may do some iterations like date formatting or JSON stringification. Maybe there's something else that can be improved but I cannot think of any performance improvement given the current code and targets.

### Proper management in a multithreading environment
Given the current built loggers, using it in a multithreaded environment is totally possible. However, working with things as a `FileLogger` or a `DatabaseLogger` would require the implementation to consider temporary locks that may affect both files or a database table.

However, this problem could become a bit less painful if we implemented the queue system:

- If time is not extra sensitive, we could just use a queue worker for each kind of logger, so Slack notifications would be handled 1 by 1, as well as the file and database logs, so there would be no locks affecting the next log.
- If time was extra sensitive and there were more than 1 queue worker, the retry engine could retry the same logging some seconds later, when the lock may have already be released. However, in this case I would still consider implementing proper lock management or job delaying if the lock is already taken.
